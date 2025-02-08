<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module, string $permission = null): Response
    {
        // Obtiene el usuario autenticado (gracias a Sanctum)
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => true, 'message'=> 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // 1. Verificar que el usuario tenga status 1
        if ($user->status != 1) {
            return response()->json(['error' => true, 'message'=>  'Inactive user'], Response::HTTP_UNAUTHORIZED);
        }

        // Obtiene los permisos del usuario en formato asociativo
        $user->permissions = $user->permissions()->get();
        $user->access      = $user->getAccess();

        if (empty($user->access)) {
            return response()->json(['error' => true, 'message'=>  'Permissions not found'], Response::HTTP_FORBIDDEN);
        }

        // 2. Verificar que el módulo exista en los permisos del usuario
        if (! array_key_exists($module, $user->access)) {
            return response()->json(['error' => true, 'message'=>  'Access not found'], Response::HTTP_FORBIDDEN);
        }

        // 3. Verificar que el permiso específico exista dentro del módulo
        if ($permission != null && !array_key_exists($permission, $user->access[$module]['permissions'])) {
            return response()->json(['error' => true, 'message'=> 'Permission not found'], Response::HTTP_FORBIDDEN);
        }

        // Si pasa todas las validaciones, continúa con la petición
        return $next($request);
    }

}
