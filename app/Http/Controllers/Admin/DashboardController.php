<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Specialty;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'hospitals' => Hospital::count(),
            'active_hospitals' => Hospital::where('is_active', true)->count(),
            'specialties' => Specialty::count(),
            'active_specialties' => Specialty::where('is_active', true)->count(),
        ];

        $recentHospitals = Hospital::latest()->take(5)->get();

        return view('admin.dashboard.index', compact('stats', 'recentHospitals'));
    }
}
