@extends('layouts.admin')

@section('page-title', 'لوحة التحكم')

@section('content')
<div class="row g-3 g-md-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-building"></i></div>
                <div class="min-w-0">
                    <div class="text-muted small">المستشفيات</div>
                    <h4 class="mb-0">{{ number_format($stats['hospitals']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <div class="icon bg-info bg-opacity-10 text-info"><i class="bi bi-people"></i></div>
                <div class="min-w-0">
                    <div class="text-muted small">المستخدمون</div>
                    <h4 class="mb-0">{{ number_format($stats['patients']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <div class="icon bg-success bg-opacity-10 text-success"><i class="bi bi-calendar-day"></i></div>
                <div class="min-w-0">
                    <div class="text-muted small">حجوزات اليوم</div>
                    <h4 class="mb-0">{{ number_format($stats['bookings_today']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-wallet2"></i></div>
                <div class="min-w-0">
                    <div class="text-muted small">أرصدة المحافظ</div>
                    <h4 class="mb-0">{{ number_format($stats['total_wallet_balance'], 2) }} د.ل</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
        <h6 class="mb-0">التقارير والإحصاءات</h6>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-bar-chart-line"></i> عرض كل التقارير
        </a>
    </div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-12 col-md-6 col-xl-4"><a href="{{ route('admin.reports.system-overview') }}" class="btn btn-outline-primary w-100 text-start"><i class="bi bi-speedometer2"></i> نظرة عامة على النظام</a></div>
            <div class="col-12 col-md-6 col-xl-4"><a href="{{ route('admin.reports.hospitals-stats') }}" class="btn btn-outline-primary w-100 text-start"><i class="bi bi-building"></i> إحصائيات المستشفيات</a></div>
            <div class="col-12 col-md-6 col-xl-4"><a href="{{ route('admin.reports.bookings-stats') }}" class="btn btn-outline-primary w-100 text-start"><i class="bi bi-calendar-check"></i> إحصائيات الحجوزات</a></div>
            <div class="col-12 col-md-6 col-xl-4"><a href="{{ route('admin.reports.financial-stats') }}" class="btn btn-outline-primary w-100 text-start"><i class="bi bi-wallet2"></i> الإحصائيات المالية</a></div>
            <div class="col-12 col-md-6 col-xl-4"><a href="{{ route('admin.reports.daily-bookings') }}" class="btn btn-outline-secondary w-100 text-start"><i class="bi bi-graph-up"></i> الحجوزات اليومية</a></div>
            <div class="col-12 col-md-6 col-xl-4"><a href="{{ route('admin.reports.monthly-bookings') }}" class="btn btn-outline-secondary w-100 text-start"><i class="bi bi-bar-chart"></i> الحجوزات الشهرية</a></div>
            <div class="col-12 col-md-6 col-xl-4"><a href="{{ route('admin.reports.user-growth') }}" class="btn btn-outline-secondary w-100 text-start"><i class="bi bi-people"></i> نمو المستخدمين</a></div>
            <div class="col-12 col-md-6 col-xl-4"><a href="{{ route('admin.reports.hospital-growth') }}" class="btn btn-outline-secondary w-100 text-start"><i class="bi bi-hospital"></i> نمو المستشفيات</a></div>
            <div class="col-12 col-md-6 col-xl-4"><a href="{{ route('admin.reports.top-hospitals') }}" class="btn btn-outline-secondary w-100 text-start"><i class="bi bi-trophy"></i> أكثر المستشفيات حجزاً</a></div>
            <div class="col-12 col-md-6 col-xl-4"><a href="{{ route('admin.reports.top-specialties') }}" class="btn btn-outline-secondary w-100 text-start"><i class="bi bi-heart-pulse"></i> أكثر التخصصات حجزاً</a></div>
        </div>
    </div>
</div>
@endsection
