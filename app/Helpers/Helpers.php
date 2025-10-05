<?php

/**
 * Funciones helper globales del sistema multiempresa
 * 
 * Registrar en composer.json:
 * "autoload": {
 *     "files": [
 *         "app/Helpers/helpers.php"
 *     ]
 * }
 */

use App\Helpers\MultiempresaHelper;
use Illuminate\Support\Facades\Auth;

if (!function_exists('grupo_actual')) {
   /**
    * Obtener el grupo empresarial actual
    */
   function grupo_actual()
   {
      return MultiempresaHelper::grupoActual();
   }
}

if (!function_exists('empresa_actual')) {
   /**
    * Obtener la empresa actual
    */
   function empresa_actual()
   {
      return MultiempresaHelper::empresaActual();
   }
}

if (!function_exists('puede')) {
   /**
    * Verificar si el usuario puede realizar una acción
    */
   function puede(string $accion, $modelo = null): bool
   {
      return MultiempresaHelper::puede($accion, $modelo);
   }
}

if (!function_exists('es_superusuario')) {
   /**
    * Verificar si el usuario actual es superusuario
    */
   function es_superusuario(): bool
   {
      $user = Auth::user();
      return $user && $user->esSuperusuario();
   }
}

if (!function_exists('es_administrador')) {
   /**
    * Verificar si el usuario es administrador (general o propietario)
    */
   function es_administrador(): bool
   {
      $user = Auth::user();
      return $user && ($user->esAdministradorGeneral() || $user->esPropietario());
   }
}

if (!function_exists('config_visual')) {
   /**
    * Obtener configuración visual del contexto actual
    */
   function config_visual(): array
   {
      return MultiempresaHelper::configuracionVisual();
   }
}

if (!function_exists('modulo_activo')) {
   /**
    * Verificar si un módulo está disponible
    */
   function modulo_activo(string $modulo): bool
   {
      return MultiempresaHelper::moduloDisponible($modulo);
   }
}

if (!function_exists('breadcrumbs')) {
   /**
    * Obtener breadcrumbs del contexto actual
    */
   function breadcrumbs(): array
   {
      return MultiempresaHelper::breadcrumbs();
   }
}

if (!function_exists('formatear_ruc')) {
   /**
    * Formatear RUC
    */
   function formatear_ruc(string $ruc): string
   {
      return MultiempresaHelper::formatearRUC($ruc);
   }
}

if (!function_exists('formatear_telefono')) {
   /**
    * Formatear teléfono peruano
    */
   function formatear_telefono(string $telefono): string
   {
      return MultiempresaHelper::formatearTelefono($telefono);
   }
}

if (!function_exists('formatear_moneda')) {
   /**
    * Formatear moneda en soles
    */
   function formatear_moneda(float $monto): string
   {
      return MultiempresaHelper::formatearMoneda($monto);
   }
}

if (!function_exists('generar_codigo')) {
   /**
    * Generar código único
    */
   function generar_codigo(string $prefijo, int $longitud = 6): string
   {
      return MultiempresaHelper::generarCodigo($prefijo, $longitud);
   }
}

if (!function_exists('validar_ruc')) {
   /**
    * Validar RUC peruano
    */
   function validar_ruc(string $ruc): bool
   {
      return MultiempresaHelper::validarRUC($ruc);
   }
}

if (!function_exists('validar_dni')) {
   /**
    * Validar DNI peruano
    */
   function validar_dni(string $dni): bool
   {
      return MultiempresaHelper::validarDNI($dni);
   }
}

if (!function_exists('registrar_actividad')) {
   /**
    * Registrar actividad en el log
    */
   function registrar_actividad(string $descripcion, $modelo = null, array $propiedades = []): void
   {
      MultiempresaHelper::registrarActividad($descripcion, $modelo, $propiedades);
   }
}

if (!function_exists('numero_a_letras')) {
   /**
    * Convertir número a letras (para facturas)
    */
   function numero_a_letras(float $numero): string
   {
      return MultiempresaHelper::numeroALetras($numero);
   }
}

if (!function_exists('plan_actual')) {
   /**
    * Obtener detalles del plan actual
    */
   function plan_actual(): ?array
   {
      return MultiempresaHelper::planActual();
   }
}

if (!function_exists('avatar_url')) {
   /**
    * Obtener URL del avatar del usuario
    */
   function avatar_url($user = null): string
   {
      $user = $user ?? Auth::user();
      return $user ? $user->avatar_url : 'https://ui-avatars.com/api/?name=Usuario&background=3B82F6&color=fff';
   }
}

if (!function_exists('nombre_mes')) {
   /**
    * Obtener nombre del mes en español
    */
   function nombre_mes(int $mes): string
   {
      return MultiempresaHelper::nombreMes($mes);
   }
}

if (!function_exists('log_seguridad')) {
   /**
    * Registrar evento de seguridad
    */
   function log_seguridad(string $evento, array $datos = []): void
   {
      $user = Auth::user();

      activity()
         ->causedBy($user)
         ->withProperties(array_merge([
            'tipo' => 'seguridad',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
         ], $datos))
         ->log("🔒 {$evento}");
   }
}

if (!function_exists('ruta_grupo')) {
   /**
    * Generar ruta del grupo actual
    */
   function ruta_grupo(string $ruta = '', array $parametros = []): string
   {
      $grupo = grupo_actual();

      if (!$grupo) {
         return '#';
      }

      $parametros = array_merge(['grupo' => $grupo->slug], $parametros);

      if (empty($ruta)) {
         return route('grupo.dashboard', $parametros);
      }

      return route("grupo.{$ruta}", $parametros);
   }
}

if (!function_exists('ruta_empresa')) {
   /**
    * Generar ruta de la empresa actual
    */
   function ruta_empresa(string $ruta = '', array $parametros = []): string
   {
      $empresa = empresa_actual();

      if (!$empresa) {
         return '#';
      }

      $parametros = array_merge([
         'grupo' => $empresa->grupoEmpresa->slug,
         'empresa' => $empresa->slug
      ], $parametros);

      if (empty($ruta)) {
         return route('empresa.dashboard', $parametros);
      }

      return route("empresa.{$ruta}", $parametros);
   }
}
