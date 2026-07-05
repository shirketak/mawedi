<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = auth('admin')->user();

        if (! $admin->is_active) {
            auth('admin')->logout();

            return redirect()->route('admin.login')
                ->with('error', 'تم إيقاف حسابك. تواصل مع مدير النظام.');
        }

        Auth::shouldUse('admin');

        return $next($request);
    }
}
