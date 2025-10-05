<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SecurityServiceProvider extends ServiceProvider
{
   /**
    * Register services.
    */
   public function register(): void
   {
      //
   }

   /**
    * Bootstrap services.
    */
   public function boot(): void
   {
      // Registrar Gates para autorización
      $this->registerGates();

      // Registrar reglas de validación personalizadas
      $this->registerValidationRules();

      // Configurar rate limiting
      $this->configureRateLimiting();
   }

   /**
    * Registrar Gates de autorización
    */
   protected function registerGates(): void
   {
      // Gate: Gestionar grupo empresarial
      Gate::define('gestionar-grupo', function ($user, $grupoId) {
         return $user->puedeGestionarGrupo($grupoId);
      });

      // Gate: Acceder a grupo empresarial
      Gate::define('acceder-grupo', function ($user, $grupoId) {
         return $user->tieneAccesoAGrupo($grupoId);
      });

      // Gate: Acceder a empresa
      Gate::define('acceder-empresa', function ($user, $empresaId) {
         return $user->tieneAccesoAEmpresa($empresaId);
      });

      // Gate: Crear empresas en un grupo
      Gate::define('crear-empresa', function ($user, $grupo) {
         if ($user->esSuperusuario()) {
            return true;
         }

         if (!$user->puedeGestionarGrupo($grupo->id)) {
            return false;
         }

         // Verificar límite de empresas del plan
         return $grupo->puedeCrearEmpresas();
      });

      // Gate: Crear usuarios en un grupo
      Gate::define('crear-usuario', function ($user, $grupo) {
         if ($user->esSuperusuario()) {
            return true;
         }

         if (!$user->puedeGestionarGrupo($grupo->id)) {
            return false;
         }

         // Verificar límite de usuarios del plan
         return $grupo->puedeCrearUsuarios();
      });

      // Gate: Modificar plan del grupo
      Gate::define('modificar-plan', function ($user, $grupo) {
         if ($user->esSuperusuario()) {
            return true;
         }

         // Solo propietarios con permiso específico
         $propietario = $grupo->propietarios()
            ->where('user_id', $user->id)
            ->wherePivot('activo', true)
            ->first();

         return $propietario && $propietario->pivot->puede_modificar_plan;
      });

      // Gate: Acceder a módulo específico
      Gate::define('usar-modulo', function ($user, $empresa, $modulo) {
         // Superusuario tiene acceso a todo
         if ($user->esSuperusuario()) {
            return true;
         }

         // Verificar acceso a la empresa
         if (!$user->tieneAccesoAEmpresa($empresa->id)) {
            return false;
         }

         // Verificar que el módulo esté activo
         return $empresa->tieneModulo($modulo);
      });

      // Gate: Ver logs del sistema
      Gate::define('ver-logs-sistema', function ($user) {
         return $user->esSuperusuario() ||
            $user->hasPermissionTo('ver_logs');
      });

      // Gate: Impersonar usuario (solo superusuario)
      Gate::define('impersonar-usuario', function ($user) {
         return $user->esSuperusuario();
      });
   }

   /**
    * Registrar reglas de validación personalizadas
    */
   protected function registerValidationRules(): void
   {
      // Validar RUC peruano
      Validator::extend('ruc_peruano', function ($attribute, $value, $parameters, $validator) {
         if (!preg_match('/^[0-9]{11}$/', $value)) {
            return false;
         }

         // Verificar que comience con 10, 15, 16, 17 o 20
         $prefijo = substr($value, 0, 2);
         return in_array($prefijo, ['10', '15', '16', '17', '20']);
      });

      // Validar DNI peruano
      Validator::extend('dni_peruano', function ($attribute, $value, $parameters, $validator) {
         return preg_match('/^[0-9]{8}$/', $value);
      });

      // Validar teléfono peruano
      Validator::extend('telefono_peruano', function ($attribute, $value, $parameters, $validator) {
         // Formato: +51 999 999 999 o 999999999
         return preg_match('/^(\+51\s?)?[0-9]{9}$/', str_replace(' ', '', $value));
      });

      // Validar slug único en contexto de grupo
      Validator::extend('slug_unico_grupo', function ($attribute, $value, $parameters, $validator) {
         $grupoId = $parameters[0] ?? null;
         $tabla = $parameters[1] ?? 'empresas';
         $exceptoId = $parameters[2] ?? null;

         if (!$grupoId) {
            return true;
         }

         $query = DB::table($tabla)
            ->where('grupo_empresa_id', $grupoId)
            ->where('slug', $value);

         if ($exceptoId) {
            $query->where('id', '!=', $exceptoId);
         }

         return !$query->exists();
      });

      // Validar límites del plan
      Validator::extend('dentro_limite_plan', function ($attribute, $value, $parameters, $validator) {
         $grupoId = $parameters[0] ?? null;
         $tipo = $parameters[1] ?? 'empresas'; // empresas o usuarios

         if (!$grupoId) {
            return true;
         }

         $grupo = \App\Models\GrupoEmpresa::find($grupoId);

         if (!$grupo) {
            return false;
         }

         if ($tipo === 'empresas') {
            return $grupo->puedeCrearEmpresas();
         }

         if ($tipo === 'usuarios') {
            return $grupo->puedeCrearUsuarios();
         }

         return true;
      });

      // Mensajes personalizados
      Validator::replacer('ruc_peruano', function ($message, $attribute, $rule, $parameters) {
         return 'El RUC debe ser válido (11 dígitos, comenzando con 10, 15, 16, 17 o 20)';
      });

      Validator::replacer('dni_peruano', function ($message, $attribute, $rule, $parameters) {
         return 'El DNI debe tener 8 dígitos';
      });

      Validator::replacer('telefono_peruano', function ($message, $attribute, $rule, $parameters) {
         return 'El teléfono debe ser válido (9 dígitos, opcional +51)';
      });

      Validator::replacer('slug_unico_grupo', function ($message, $attribute, $rule, $parameters) {
         return 'Este identificador ya existe en el grupo empresarial';
      });

      Validator::replacer('dentro_limite_plan', function ($message, $attribute, $rule, $parameters) {
         $tipo = $parameters[1] ?? 'elementos';
         return "Has alcanzado el límite de {$tipo} de tu plan actual";
      });
   }

   /**
    * Configurar rate limiting
    */
   protected function configureRateLimiting(): void
   {
      // Límite para login
      RateLimiter::for('login', function (Request $request) {
         $email = (string) $request->email;

         return Limit::perMinute(5)->by($email . $request->ip())
            ->response(function () {
               return response()->json([
                  'message' => 'Demasiados intentos de inicio de sesión. Intenta de nuevo en 1 minuto.'
               ], 429);
            });
      });

      // Límite para API
      RateLimiter::for('api', function (Request $request) {
         return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
      });

      // Límite para acciones sensibles
      RateLimiter::for('acciones-sensibles', function (Request $request) {
         return Limit::perMinute(10)->by($request->user()->id ?? $request->ip())
            ->response(function () {
               return back()->with('error', 'Demasiadas acciones en poco tiempo. Espera un momento.');
            });
      });

      // Límite para creación de recursos
      RateLimiter::for('crear-recursos', function (Request $request) {
         return Limit::perHour(100)->by($request->user()->grupo_empresa_id ?? $request->ip());
      });
   }
}
