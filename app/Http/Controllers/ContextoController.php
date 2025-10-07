<?php

namespace App\Http\Controllers;

use App\Models\Workspace\Empresa;
use App\Models\Workspace\Local;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Controlador para gestionar el contexto de empresa y local del usuario
 * 
 * Este controlador maneja:
 * - Selector de contexto (empresa y local)
 * - Cambio de contexto
 * - Validación de acceso
 * - Persistencia en sesión y modelo
 */
class ContextoController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar selector de contexto
     * 
     * Permite al usuario seleccionar la empresa y local
     * en el que desea trabajar.
     * 
     * @return View
     */
    public function selector(): View
    {
        $user = Auth::user();

        // Obtener empresas con acceso
        $empresas = $user->getEmpresasConAcceso();

        // Si no tiene empresas, mostrar mensaje
        if ($empresas->isEmpty()) {
            return view('contexto.sin-empresas', [
                'user' => $user,
            ]);
        }

        // Obtener contexto actual
        $contextoActual = $user->getContextoActual();
        $empresaActual = $contextoActual['empresa_id'];
        $localActual = $contextoActual['local_id'];

        // Obtener URL de redirección (si viene del middleware)
        $redirectTo = session('redirect_to', route('dashboard'));

        return view('contexto.selector', [
            'user' => $user,
            'empresas' => $empresas,
            'empresaActual' => $empresaActual,
            'localActual' => $localActual,
            'redirectTo' => $redirectTo,
        ]);
    }

    /**
     * Obtener locales de una empresa (AJAX)
     * 
     * Retorna los locales activos de una empresa específica
     * para cargarlos dinámicamente en el selector.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocales(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|integer|exists:empresas,id',
        ]);

        $user = Auth::user();
        $empresaId = $request->empresa_id;

        // Verificar acceso
        if (!$user->tieneAccesoAEmpresa($empresaId)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a esta empresa.',
            ], 403);
        }

        // Obtener empresa y sus locales
        $empresa = Empresa::find($empresaId);
        $locales = $empresa->getLocalesActivos();

        return response()->json([
            'success' => true,
            'locales' => $locales->map(function ($local) {
                return [
                    'id' => $local->id,
                    'nombre' => $local->nombre,
                    'direccion' => $local->direccion,
                    'es_principal' => $local->pivot->es_principal ?? false,
                ];
            }),
        ]);
    }

    /**
     * Cambiar el contexto de empresa y local
     * 
     * Valida el acceso, actualiza el contexto en sesión
     * y en el modelo User, y redirige al destino.
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function cambiar(Request $request): RedirectResponse
    {
        $request->validate([
            'empresa_id' => 'required|integer|exists:empresas,id',
            'local_id' => 'nullable|integer|exists:locales,id',
            'redirect_to' => 'nullable|url',
        ]);

        $user = Auth::user();
        $empresaId = $request->empresa_id;
        $localId = $request->local_id;

        // Verificar acceso a la empresa
        if (!$user->tieneAccesoAEmpresa($empresaId)) {
            return back()->with('error', 'No tienes acceso a la empresa seleccionada.');
        }

        // Obtener empresa
        $empresa = Empresa::find($empresaId);

        // Verificar que la empresa esté activa
        if (!$empresa->estaActiva()) {
            return back()->with('error', 'La empresa seleccionada no está activa.');
        }

        // Si se proporciona local, verificar que pertenezca a la empresa
        if ($localId) {
            if (!$empresa->tieneLocal($localId)) {
                return back()->with('error', 'El local seleccionado no pertenece a esta empresa.');
            }
        }

        // Cambiar contexto usando el método del modelo User
        $resultado = $user->cambiarContexto($empresaId, $localId);

        if (!$resultado) {
            return back()->with('error', 'No se pudo cambiar el contexto. Intenta nuevamente.');
        }

        // Obtener nombre del local si existe
        $local = $localId ? Local::find($localId) : null;
        $mensajeLocal = $local ? " - Local: {$local->nombre}" : '';

        // Log de cambio de contexto
        activity()
            ->performedOn($empresa)
            ->causedBy($user)
            ->withProperties([
                'empresa_id' => $empresaId,
                'empresa_nombre' => $empresa->nombre,
                'local_id' => $localId,
                'local_nombre' => $local?->nombre,
                'ip' => $request->ip(),
            ])
            ->log('Usuario cambió de contexto');

        // Limpiar URL de redirección de la sesión
        session()->forget('redirect_to');

        // Redirigir al destino
        $redirectTo = $request->redirect_to ?? route('dashboard');

        return redirect($redirectTo)->with('success', "Contexto cambiado a: {$empresa->nombre}{$mensajeLocal}");
    }

    /**
     * Limpiar el contexto actual
     * 
     * Elimina el contexto de sesión y redirige al selector.
     * Útil para cuando el usuario quiere cambiar de empresa.
     * 
     * @return RedirectResponse
     */
    public function limpiar(): RedirectResponse
    {
        $user = Auth::user();

        // Limpiar contexto de sesión
        session()->forget(['contexto_empresa_id', 'contexto_local_id']);

        // Log
        activity()
            ->causedBy($user)
            ->withProperties([
                'ip' => request()->ip(),
            ])
            ->log('Usuario limpió su contexto');

        return redirect()->route('contexto.selector')
            ->with('info', 'Contexto limpiado. Selecciona una nueva empresa.');
    }

    /**
     * Obtener información del contexto actual (AJAX)
     * 
     * Retorna el contexto actual del usuario en formato JSON.
     * Útil para componentes dinámicos que necesitan el contexto.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContextoActual()
    {
        $user = Auth::user();
        $contexto = $user->getContextoActual();

        $empresa = $contexto['empresa_id'] ? $user->getEmpresaContexto() : null;
        $local = $contexto['local_id'] ? $user->getLocalContexto() : null;

        return response()->json([
            'success' => true,
            'contexto' => [
                'empresa_id' => $contexto['empresa_id'],
                'local_id' => $contexto['local_id'],
                'empresa' => $empresa ? [
                    'id' => $empresa->id,
                    'nombre' => $empresa->nombre,
                    'slug' => $empresa->slug,
                    'ruc' => $empresa->ruc,
                    'logo_url' => $empresa->logo_url,
                ] : null,
                'local' => $local ? [
                    'id' => $local->id,
                    'nombre' => $local->nombre,
                    'direccion' => $local->direccion,
                ] : null,
            ],
        ]);
    }

    /**
     * Cambio rápido de empresa (mantiene local si es posible)
     * 
     * Permite cambiar rápidamente de empresa sin pasar por el selector.
     * Útil para menús desplegables de cambio rápido.
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function cambioRapido(Request $request): RedirectResponse
    {
        $request->validate([
            'empresa_id' => 'required|integer|exists:empresas,id',
        ]);

        $user = Auth::user();
        $empresaId = $request->empresa_id;

        // Verificar acceso
        if (!$user->tieneAccesoAEmpresa($empresaId)) {
            return back()->with('error', 'No tienes acceso a esta empresa.');
        }

        // Obtener empresa
        $empresa = Empresa::find($empresaId);

        // Verificar si el local actual pertenece a la nueva empresa
        $contextoActual = $user->getContextoActual();
        $localActual = $contextoActual['local_id'];
        
        $mantenerLocal = false;
        if ($localActual && $empresa->tieneLocal($localActual)) {
            $mantenerLocal = true;
        }

        // Cambiar contexto
        $localId = $mantenerLocal ? $localActual : null;
        $user->cambiarContexto($empresaId, $localId);

        // Log
        activity()
            ->performedOn($empresa)
            ->causedBy($user)
            ->withProperties([
                'empresa_id' => $empresaId,
                'local_id' => $localId,
                'tipo' => 'cambio_rapido',
                'ip' => $request->ip(),
            ])
            ->log('Usuario realizó cambio rápido de empresa');

        return back()->with('success', "Contexto cambiado a: {$empresa->nombre}");
    }
}
