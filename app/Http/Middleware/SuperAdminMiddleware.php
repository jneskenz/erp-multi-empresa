<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión.');
        }

        // Verificar si es superadministrador
        if (!$request->user()->isSuperAdmin()) {
            abort(403, 'Acceso denegado. Solo superadministradores pueden acceder a esta sección.');
        }

        return $next($request);
    }
}
