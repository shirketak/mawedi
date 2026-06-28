<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureHospitalUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('hospital')->user();

        if (! $user || ! $user->is_active) {
            auth('hospital')->logout();

            return redirect()->route('hospital.login')
                ->with('error', 'يرجى تسجيل الدخول للمتابعة.');
        }

        if (! $user->hospital?->is_active) {
            auth('hospital')->logout();

            return redirect()->route('hospital.login')
                ->with('error', 'حساب المستشفى غير مفعّل. تواصل مع الإدارة.');
        }

        Auth::shouldUse('hospital');

        return $next($request);
    }
}
