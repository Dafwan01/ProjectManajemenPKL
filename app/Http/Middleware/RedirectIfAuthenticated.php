<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                $userRole = $user->role instanceof \UnitEnum
                    ? $user->role->value
                    : $user->role;

                if ($userRole === UserRole::PKL->value) {
                    return redirect()->route('user.presensi');
                }

                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}