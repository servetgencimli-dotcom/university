<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * Arguments are role names, or `permission:<name>` tokens that grant access
     * only to routes explicitly declaring that permission (e.g. `role:system_admin,permission:cms.manage`).
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$rolesAndPermissions): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isSystemAdmin() || $user->hasPermission('system.all')) {
            return $next($request);
        }

        foreach ($rolesAndPermissions as $requirement) {
            if (str_starts_with($requirement, 'permission:')) {
                if ($user->hasPermission(substr($requirement, strlen('permission:')))) {
                    return $next($request);
                }

                continue;
            }

            if ($user->role === $requirement) {
                return $next($request);
            }
        }

        abort(403, 'Bu əməliyyatı icra etmək üçün tələb olunan rol və ya səlahiyyətiniz yoxdur.');
    }
}
