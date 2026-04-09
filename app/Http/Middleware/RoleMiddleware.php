<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!in_array($request->user()->role, $roles)) {
            $dashboards = config('roles.dashboards', []);
            $userRole = $request->user()->role;

            if (array_key_exists($userRole, $dashboards)) {
                $targetRoute = $dashboards[$userRole];

                // Prevent redirect loop if the current route is already the target dashboard
                if ($request->route()->getName() !== $targetRoute) {
                    return redirect()->route($targetRoute)
                        ->with('error', 'You do not have permission to access that page.');
                }
            }

            abort(403, 'Unauthorized.');
        }

        if (!$request->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}
