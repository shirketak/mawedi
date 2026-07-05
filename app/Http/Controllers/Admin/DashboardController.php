<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AdminReportService $reportService,
    ) {}

    public function index(): View
    {
        abort_unless(auth('admin')->user()?->hasPermission('dashboard'), 403);

        $stats = $this->reportService->dashboardStats();

        return view('admin.dashboard.index', compact('stats'));
    }
}
