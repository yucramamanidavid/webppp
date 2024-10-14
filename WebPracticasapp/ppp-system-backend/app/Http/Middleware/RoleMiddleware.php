<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Maneja la solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // Verifica si el usuario tiene uno de los roles permitidos
        if ($user && in_array($user->role, $roles)) {
            return $next($request);
        }

        // Si el rol no es permitido, responde con un mensaje de error
        return response()->json(['message' => 'No tienes permiso para acceder a esta ruta.'], 403);
    }
}
