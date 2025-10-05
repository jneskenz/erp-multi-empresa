<?php

namespace App\Helpers;

use App\Models\GrupoEmpresa;
use App\Models\Workspace\Empresa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MultiempresaHelper
{
   /**
    * Obtener el grupo empresarial actual desde el contexto
    */
   public static function grupoActual(): ?GrupoEmpresa
   {
      $user = Auth::user();

      if (!$user) {
         return null;
      }

      // Desde el request (inyectado por middleware)
      if ($grupo = request()->get('grupoEmpresa')) {
         return $grupo;
      }

      // Desde el usuario
      return $user->grupoEmpresa;
   }

   /**
    * Obtener la empresa actual desde el contexto
    */
   public static function empresaActual(): ?Empresa
   {
      $user = Auth::user();

      if (!$user) {
         return null;
      }

      // Desde el request (inyectado por middleware)
      if ($empresa = request()->get('empresa')) {
         return $empresa;
      }

      // Desde el usuario
      return $user->getEmpresaPrincipal();
   }

   /**
    * Verificar si el usuario actual puede realizar una acción
    */
   public static function puede(string $accion, $modelo = null): bool
   {
      $user = Auth::user();

      if (!$user) {
         return false;
      }

      if ($modelo) {
         return $user->can($accion, $modelo);
      }

      return $user->hasPermissionTo($accion);
   }

   /**
    * Generar slug único para una tabla y contexto
    */
   public static function generarSlugUnico(string $texto, string $tabla, int $grupoId, ?int $exceptoId = null): string
   {
      $slug = Str::slug($texto);
      $originalSlug = $slug;
      $count = 1;

      while (true) {
         $query = DB::table($tabla)
            ->where('grupo_empresa_id', $grupoId)
            ->where('slug', $slug);

         if ($exceptoId) {
            $query->where('id', '!=', $exceptoId);
         }

         if (!$query->exists()) {
            break;
         }

         $slug = $originalSlug . '-' . $count;
         $count++;
      }

      return $slug;
   }

   /**
    * Formatear RUC
    */
   public static function formatearRUC(string $ruc): string
   {
      return preg_replace('/(\d{2})(\d{9})/', '$1-$2', $ruc);
   }

   /**
    * Formatear teléfono peruano
    */
   public static function formatearTelefono(string $telefono): string
   {
      // Eliminar espacios y caracteres no numéricos excepto +
      $telefono = preg_replace('/[^0-9+]/', '', $telefono);

      // Si no tiene +51, agregarlo
      if (!str_starts_with($telefono, '+51')) {
         $telefono = '+51' . ltrim($telefono, '0');
      }

      // Formato: +51 999 999 999
      return preg_replace('/(\+51)(\d{3})(\d{3})(\d{3})/', '$1 $2 $3 $4', $telefono);
   }

   /**
    * Obtener configuración visual del contexto actual
    */
   public static function configuracionVisual(): array
   {
      $user = Auth::user();

      if (!$user) {
         return self::configuracionVisualPorDefecto();
      }

      return Cache::remember('config_visual:user:' . $user->id, 3600, function () use ($user) {
         return $user->getConfiguracionVisual();
      });
   }

   /**
    * Configuración visual por defecto
    */
   public static function configuracionVisualPorDefecto(): array
   {
      return [
         'tema' => 'light',
         'color_primario' => '#3B82F6',
         'color_secundario' => '#10B981',
         'fuente' => 'Inter',
         'logo' => null,
      ];
   }

   /**
    * Verificar si un módulo está disponible en el contexto actual
    */
   public static function moduloDisponible(string $modulo): bool
   {
      $empresa = self::empresaActual();

      if (!$empresa) {
         return false;
      }

      return $empresa->tieneModulo($modulo);
   }

   /**
    * Obtener breadcrumbs del contexto actual
    */
   public static function breadcrumbs(): array
   {
      $breadcrumbs = [];
      $user = Auth::user();

      if (!$user) {
         return $breadcrumbs;
      }

      // Inicio
      $breadcrumbs[] = [
         'nombre' => 'Inicio',
         'url' => route('dashboard'),
         'icono' => 'home',
      ];

      // Grupo empresarial
      if ($grupo = self::grupoActual()) {
         $breadcrumbs[] = [
            'nombre' => $grupo->nombre,
            'url' => route('grupo.dashboard', ['grupo' => $grupo->slug]),
            'icono' => 'building',
         ];
      }

      // Empresa
      if ($empresa = self::empresaActual()) {
         $breadcrumbs[] = [
            'nombre' => $empresa->nombre,
            'url' => route('empresa.dashboard', [
               'grupo' => $empresa->grupoEmpresa->slug,
               'empresa' => $empresa->slug
            ]),
            'icono' => 'briefcase',
         ];
      }

      return $breadcrumbs;
   }

   /**
    * Obtener estadísticas rápidas del contexto
    */
   public static function estadisticasRapidas(): array
   {
      $user = Auth::user();

      if (!$user) {
         return [];
      }

      $cacheKey = 'stats:user:' . $user->id;

      return Cache::remember($cacheKey, 300, function () use ($user) {
         $stats = [];

         if ($grupo = self::grupoActual()) {
            $stats['empresas'] = $grupo->empresas()->count();
            $stats['usuarios'] = $grupo->usuarios()->count();
            $stats['sedes'] = $grupo->sedes()->count();
         }

         if ($empresa = self::empresaActual()) {
            // Aquí agregar estadísticas específicas de la empresa
            // según los módulos activos
            if ($empresa->tieneModulo('erp')) {
               $stats['productos'] = 0; // Implementar cuando exista el modelo
               $stats['clientes'] = 0;
            }
         }

         return $stats;
      });
   }

   /**
    * Limpiar caché de un usuario específico
    */
   public static function limpiarCacheUsuario(int $userId): void
   {
      Cache::forget('config_visual:user:' . $userId);
      Cache::forget('stats:user:' . $userId);
   }

   /**
    * Limpiar caché de un grupo empresarial
    */
   public static function limpiarCacheGrupo(int $grupoId): void
   {
      Cache::forget('stats:grupo:' . $grupoId);
   }

   /**
    * Validar RUC peruano
    */
   public static function validarRUC(string $ruc): bool
   {
      if (!preg_match('/^[0-9]{11}$/', $ruc)) {
         return false;
      }

      $prefijo = substr($ruc, 0, 2);
      return in_array($prefijo, ['10', '15', '16', '17', '20']);
   }

   /**
    * Validar DNI peruano
    */
   public static function validarDNI(string $dni): bool
   {
      return preg_match('/^[0-9]{8}$/', $dni);
   }

   /**
    * Obtener el plan actual del grupo
    */
   public static function planActual(): ?array
   {
      $grupo = self::grupoActual();

      if (!$grupo) {
         return null;
      }

      return self::obtenerDetallesPlan($grupo->plan_actual);
   }

   /**
    * Obtener detalles de un plan
    */
   public static function obtenerDetallesPlan(string $plan): array
   {
      $planes = [
         'basico' => [
            'nombre' => 'Básico',
            'precio' => 99.00,
            'max_empresas' => 1,
            'max_usuarios' => 10,
            'modulos' => ['erp'],
            'soporte' => 'email',
         ],
         'profesional' => [
            'nombre' => 'Profesional',
            'precio' => 299.00,
            'max_empresas' => 3,
            'max_usuarios' => 50,
            'modulos' => ['erp', 'crm', 'web'],
            'soporte' => 'prioritario',
         ],
         'empresarial' => [
            'nombre' => 'Empresarial',
            'precio' => 599.00,
            'max_empresas' => 10,
            'max_usuarios' => 200,
            'modulos' => ['erp', 'crm', 'rrhh', 'web'],
            'soporte' => '24/7',
         ],
      ];

      return $planes[$plan] ?? $planes['basico'];
   }

   /**
    * Registrar actividad personalizada
    */
   public static function registrarActividad(string $descripcion, $modelo = null, array $propiedades = []): void
   {
      $user = Auth::user();

      if (!$user) {
         return;
      }

      $log = activity()
         ->causedBy($user)
         ->withProperties(array_merge([
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'grupo_empresa_id' => $user->grupo_empresa_id,
            'empresa_id' => $user->empresa_id,
         ], $propiedades))
         ->log($descripcion);

      if ($modelo) {
         $log->performedOn($modelo);
      }
   }

   /**
    * Generar código único para entidades
    */
   public static function generarCodigo(string $prefijo, int $longitud = 6): string
   {
      $numero = str_pad(random_int(0, pow(10, $longitud) - 1), $longitud, '0', STR_PAD_LEFT);
      return strtoupper($prefijo) . '-' . $numero;
   }

   /**
    * Formatear moneda (soles peruanos)
    */
   public static function formatearMoneda(float $monto): string
   {
      return 'S/ ' . number_format($monto, 2, '.', ',');
   }

   /**
    * Obtener nombre del mes en español
    */
   public static function nombreMes(int $mes): string
   {
      $meses = [
         1 => 'Enero',
         2 => 'Febrero',
         3 => 'Marzo',
         4 => 'Abril',
         5 => 'Mayo',
         6 => 'Junio',
         7 => 'Julio',
         8 => 'Agosto',
         9 => 'Septiembre',
         10 => 'Octubre',
         11 => 'Noviembre',
         12 => 'Diciembre'
      ];

      return $meses[$mes] ?? '';
   }

   /**
    * Convertir número a letras (para facturas)
    */
   public static function numeroALetras(float $numero): string
   {
      $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
      $parteEntera = floor($numero);
      $parteDecimal = round(($numero - $parteEntera) * 100);

      $letras = ucfirst($formatter->format($parteEntera));

      if ($parteDecimal > 0) {
         $letras .= ' con ' . str_pad($parteDecimal, 2, '0', STR_PAD_LEFT) . '/100';
      } else {
         $letras .= ' con 00/100';
      }

      return $letras . ' SOLES';
   }
}
