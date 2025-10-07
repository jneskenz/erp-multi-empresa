<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\View;

/**
 * Middleware para verificar y gestionar el contexto de empresa del usuario
 * 
 * Este middleware:
 * 1. Verifica que el usuario tenga un contexto de empresa activo
 * 2. Si no tiene contexto, redirige al selector de contexto
 * 3. Valida que el usuario tenga acceso a la empresa del contexto
 * 4. Carga la información del contexto en la sesión y vistas
 * 5. Sincroniza el contexto de sesión con el modelo User
 * 
 * Uso:
 * Route::middleware(['auth', 'contexto.empresa'])->group(function () {
 *     // Rutas que requieren contexto de empresa
 * });
 */
class VerificarContextoEmpresa
{
    /**
     * Rutas que no requieren contexto de empresa
     */
    protected array $except = [
        'contexto.selector',
        'contexto.cambiar',
        'logout',
        'login',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar autenticación
        if (!$user = $request->user()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para continuar.');
        }

        // Verificar si la ruta está exceptuada
        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        // Superusuario no necesita contexto (puede ver todo)
        if ($user->esSuperusuario()) {
            $this->compartirContextoEnVistas($user, null, null);
            return $next($request);
        }

        // Obtener contexto actual del usuario
        $contexto = $user->getContextoActual();
        $empresaId = $contexto['empresa_id'];
        $localId = $contexto['local_id'];

        // Si no hay contexto de empresa, redirigir al selector
        if (!$empresaId) {
            // Verificar si el usuario tiene empresas asignadas
            $empresasDisponibles = $user->getEmpresasConAcceso();

            if ($empresasDisponibles->isEmpty()) {
                return redirect()->route('dashboard')
                    ->with('error', 'No tienes empresas asignadas. Contacta con tu administrador.');
            }

            // Si solo tiene una empresa, asignarla automáticamente
            if ($empresasDisponibles->count() === 1) {
                $empresa = $empresasDisponibles->first();
                $user->cambiarContexto($empresa->id);
                
                return redirect($request->fullUrl())
                    ->with('success', "Contexto establecido automáticamente: {$empresa->nombre}");
            }

            // Redirigir al selector de contexto
            return redirect()->route('contexto.selector')
                ->with('warning', 'Por favor, selecciona una empresa para continuar.')
                ->with('redirect_to', $request->fullUrl());
        }

        // Verificar que el usuario tenga acceso a la empresa del contexto
        if (!$user->tieneAccesoAEmpresa($empresaId)) {
            // Limpiar contexto inválido
            session()->forget(['contexto_empresa_id', 'contexto_local_id']);
            
            // Log de seguridad
            activity()
                ->causedBy($user)
                ->withProperties([
                    'empresa_id' => $empresaId,
                    'local_id' => $localId,
                    'ip' => $request->ip(),
                ])
                ->log('Intento de acceso a empresa sin permisos en contexto');

            return redirect()->route('contexto.selector')
                ->with('error', 'El contexto seleccionado no es válido. Por favor, selecciona una empresa.')
                ->with('redirect_to', $request->fullUrl());
        }

        // Cargar modelos de empresa y local
        $empresa = \App\Models\Workspace\Empresa::find($empresaId);
        $local = $localId ? \App\Models\Workspace\Local::find($localId) : null;

        // Verificar que la empresa exista y esté activa
        if (!$empresa || !$empresa->estaActiva()) {
            session()->forget(['contexto_empresa_id', 'contexto_local_id']);
            
            return redirect()->route('contexto.selector')
                ->with('error', 'La empresa seleccionada no está disponible.')
                ->with('redirect_to', $request->fullUrl());
        }

        // Si hay local, verificar que pertenezca a la empresa
        if ($local && !$empresa->tieneLocal($local->id)) {
            // Limpiar local inválido pero mantener empresa
            session()->forget('contexto_local_id');
            $user->local_id = null;
            $user->save();
            
            $local = null;
        }

        // Compartir contexto en vistas
        $this->compartirContextoEnVistas($user, $empresa, $local);

        // Inyectar contexto en el request para uso en controladores
        $request->merge([
            'contexto_empresa' => $empresa,
            'contexto_local' => $local,
        ]);

        return $next($request);
    }

    /**
     * Compartir contexto en todas las vistas
     */
    protected function compartirContextoEnVistas($user, $empresa, $local): void
    {
        View::share('contextoEmpresa', $empresa);
        View::share('contextoLocal', $local);
        View::share('tieneContexto', $empresa !== null);
        
        // Compartir información adicional
        if ($empresa) {
            View::share('contextoGrupo', $empresa->grupoEmpresa);
            View::share('localesDisponibles', $empresa->getLocalesActivos());
        }
    }

    /**
     * Verificar si la ruta actual está en la lista de excepciones
     */
    protected function inExceptArray(Request $request): bool
    {
        foreach ($this->except as $except) {
            if ($request->routeIs($except)) {
                return true;
            }
        }

        return false;
    }
}
