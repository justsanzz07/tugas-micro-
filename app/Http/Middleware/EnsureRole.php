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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            auth()->logout();

            return redirect()->route('login')->withErrors(['email' => 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.']);
        }

        // Administrator memiliki akses masuk dan meninjau seluruh role/portal
        if ($user->isAdmin()) {
            return $next($request);
        }

        $hasAllowedRole = false;
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                $hasAllowedRole = true;
                break;
            }
        }

        if (! $hasAllowedRole) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses portal ini.');
        }

        return $next($request);
    }
}
