<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hospital\HospitalLoginRequest;
use App\Models\Booking;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('hospital.auth.login');
    }

    public function login(HospitalLoginRequest $request): RedirectResponse
    {
        if (! auth('hospital')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة.'])->onlyInput('email');
        }

        $user = auth('hospital')->user();

        if (! $user->is_active || ! $user->hospital?->is_active) {
            auth('hospital')->logout();

            return back()->withErrors(['email' => 'حساب المستشفى غير مفعّل.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('hospital.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        auth('hospital')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('hospital.login');
    }
}
