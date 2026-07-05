@extends('layouts.admin')

@section('page-title', 'التقارير والإحصاءات')

@section('content')
@include('partials.report-nav')

<div class="row g-3 g-md-4">
    @php
        $reportLinks = [
            ['route' => 'admin.reports.system-overview', 'title' => 'نظرة عامة على النظام', 'desc' => 'جميع مؤشرات النظام في صفحة واحدة', 'icon' => 'bi-speedometer2', 'color' => 'primary'],
            ['route' => 'admin.reports.hospitals-stats', 'title' => 'إحصائيات المستشفيات', 'desc' => 'عدد المستشفيات والأطباء والتخصصات', 'icon' => 'bi-building', 'color' => 'success'],
            ['route' => 'admin.reports.bookings-stats', 'title' => 'إحصائيات الحجوزات', 'desc' => 'حجوزات اليوم والشهر والمكتملة والملغاة', 'icon' => 'bi-calendar-check', 'color' => 'info'],
            ['route' => 'admin.reports.financial-stats', 'title' => 'الإحصائيات المالية', 'desc' => 'الاشتراكات وأرصدة المحافظ', 'icon' => 'bi-wallet2', 'color' => 'warning'],
            ['route' => 'admin.reports.daily-bookings', 'title' => 'الحجوزات اليومية', 'desc' => 'رسم بياني للحجوزات خلال 30 يوم', 'icon' => 'bi-graph-up', 'color' => 'primary'],
            ['route' => 'admin.reports.monthly-bookings', 'title' => 'الحجوزات الشهرية', 'desc' => 'رسم بياني للحجوزات خلال 12 شهر', 'icon' => 'bi-bar-chart', 'color' => 'primary'],
            ['route' => 'admin.reports.user-growth', 'title' => 'نمو المستخدمين', 'desc' => 'تطور عدد مستخدمي التطبيق', 'icon' => 'bi-people', 'color' => 'success'],
            ['route' => 'admin.reports.hospital-growth', 'title' => 'نمو المستشفيات', 'desc' => 'تطور عدد المستشفيات المسجّلة', 'icon' => 'bi-hospital', 'color' => 'warning'],
            ['route' => 'admin.reports.top-hospitals', 'title' => 'أكثر المستشفيات حجزاً', 'desc' => 'ترتيب المستشفيات حسب عدد الحجوزات', 'icon' => 'bi-trophy', 'color' => 'info'],
            ['route' => 'admin.reports.top-specialties', 'title' => 'أكثر التخصصات حجزاً', 'desc' => 'ترتيب التخصصات حسب عدد الحجوزات', 'icon' => 'bi-heart-pulse', 'color' => 'danger'],
        ];
    @endphp

    @foreach($reportLinks as $report)
    <div class="col-12 col-md-6 col-xl-4">
        <a href="{{ route($report['route']) }}" class="text-decoration-none">
            <div class="card h-100 report-link-card">
                <div class="card-body d-flex gap-3 align-items-start">
                    <div class="icon bg-{{ $report['color'] }} bg-opacity-10 text-{{ $report['color'] }} flex-shrink-0">
                        <i class="bi {{ $report['icon'] }}"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-dark">{{ $report['title'] }}</h6>
                        <p class="text-muted small mb-0">{{ $report['desc'] }}</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>
@endsection
