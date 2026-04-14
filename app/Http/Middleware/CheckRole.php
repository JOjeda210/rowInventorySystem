<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Verifica si el usuario autenticado tiene uno de los roles permitidos.
     * Los roles pueden ser pasados separados por coma como parametro del middleware.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Verificar si el usuario tiene uno de los roles permitidos
        if ($user->hasRole($roles)) {
            return $next($request);
        }

        // Usuario no tiene los permisos necesarios
        return redirect()
            ->route('dashboard')
            ->with('error', 'No tienes permisos para acceder a esta seccion.');
    }
}
