<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckModulePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next, string $module = '', string $permission = 'view')
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Super Admin tiene acceso a todo
        if ($user->role?->name === 'Super Admin') {
            return $next($request);
        }

        // Verificar si el usuario tiene permiso en el módulo
        $hasPermission = $user->role?->modulePermissions()
            ->whereHas('module', function ($query) use ($module) {
                $query->where('slug', $module)->where('is_active', true);
            })
            ->whereHas('permission', function ($query) use ($permission) {
                $query->where('slug', $permission);
            })
            ->exists();

        if (!$hasPermission) {
            return response()->json([
                'message' => "No tienes permiso para {$permission} en el módulo {$module}",
            ], 403);
        }

        return $next($request);
    }
}
