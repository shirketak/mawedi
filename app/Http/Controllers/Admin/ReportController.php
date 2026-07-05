<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private readonly AdminReportService $reportService,
    ) {}

    public function index(): View
    {
        $this->ensureCanViewReports();

        return view('admin.reports.index');
    }

    public function systemOverview(): View
    {
        $this->ensureCanViewReports();

        return view('admin.reports.system-overview', [
            'stats' => $this->reportService->dashboardStats(),
        ]);
    }

    public function hospitalsStats(): View
    {
        $this->ensureCanViewReports();

        return view('admin.reports.hospitals-stats', [
            'stats' => $this->reportService->hospitalStats(),
        ]);
    }

    public function bookingsStats(): View
    {
        $this->ensureCanViewReports();

        return view('admin.reports.bookings-stats', [
            'stats' => $this->reportService->bookingStats(),
        ]);
    }

    public function financialStats(): View
    {
        $this->ensureCanViewReports();

        return view('admin.reports.financial-stats', [
            'stats' => $this->reportService->financialStats(),
        ]);
    }

    public function dailyBookings(Request $request): View
    {
        $this->ensureCanViewReports();

        $days = (int) $request->get('days', 30);

        return view('admin.reports.daily-bookings', [
            'days' => $days,
            'chart' => $this->reportService->dailyBookingsChart($days),
        ]);
    }

    public function monthlyBookings(Request $request): View
    {
        $this->ensureCanViewReports();

        $months = (int) $request->get('months', 12);

        return view('admin.reports.monthly-bookings', [
            'months' => $months,
            'chart' => $this->reportService->monthlyBookingsChart($months),
        ]);
    }

    public function userGrowth(Request $request): View
    {
        $this->ensureCanViewReports();

        $months = (int) $request->get('months', 12);

        return view('admin.reports.user-growth', [
            'months' => $months,
            'chart' => $this->reportService->userGrowthChart($months),
        ]);
    }

    public function hospitalGrowth(Request $request): View
    {
        $this->ensureCanViewReports();

        $months = (int) $request->get('months', 12);

        return view('admin.reports.hospital-growth', [
            'months' => $months,
            'chart' => $this->reportService->hospitalGrowthChart($months),
        ]);
    }

    public function topHospitals(Request $request): View
    {
        $this->ensureCanViewReports();

        $limit = (int) $request->get('limit', 10);

        return view('admin.reports.top-hospitals', [
            'limit' => $limit,
            'items' => $this->reportService->topHospitalsByBookings($limit),
        ]);
    }

    public function topSpecialties(Request $request): View
    {
        $this->ensureCanViewReports();

        $limit = (int) $request->get('limit', 10);

        return view('admin.reports.top-specialties', [
            'limit' => $limit,
            'items' => $this->reportService->topSpecialtiesByBookings($limit),
        ]);
    }

    private function ensureCanViewReports(): void
    {
        $admin = auth('admin')->user();

        abort_unless(
            $admin && ($admin->hasPermission('reports') || $admin->hasPermission('dashboard')),
            403
        );
    }
}
