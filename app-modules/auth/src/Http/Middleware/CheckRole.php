<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Para rutas web, verificar si hay token en localStorage (se maneja en frontend)
        // Para rutas API, verificar autenticación con Sanctum
        
        if ($request->expectsJson() || $request->is('api/*')) {
            // Rutas API
            if (!Auth::guard('sanctum')->check()) {
                return response()->json(['error' => 'No autenticado'], 401);
            }
            
            $user = Auth::guard('sanctum')->user();
        } else {
            // Rutas web - permitir acceso (la verificación se hace en frontend)
            // El frontend redirige si no hay token
            return $next($request);
        }
        
        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'No tiene permisos'], 403);
            }
            abort(403, 'No tiene permisos para acceder a esta sección');
        }

        return $next($request);
    }
}
