@extends('layouts.admin')

@section('page-title', 'نظرة عامة على النظام')

@section('content')
@include('partials.report-nav')

@php
    $cards = [
        ['key' => 'hospitals', 'label' => 'المستشفيات', 'icon' => 'bi-building', 'color' => 'primary'],
        ['key' => 'active_hospitals', 'label' => 'مستشفيات مفعّلة', 'icon' => 'bi-check-circle', 'color' => 'success'],
        ['key' => 'inactive_hospitals', 'label' => 'مستشفيات موقوفة', 'icon' => 'bi-x-circle', 'color' => 'secondary'],
        ['key' => 'patients', 'label' => 'المستخدمون', 'icon' => 'bi-people', 'color' => 'info'],
        ['key' => 'doctors', 'label' => 'الأطباء', 'icon' => 'bi-person-badge', 'color' => 'success'],
        ['key' => 'specialties', 'label' => 'التخصصات', 'icon' => 'bi-heart-pulse', 'color' => 'warning'],
        ['key' => 'bookings_today', 'label' => 'حجوزات اليوم', 'icon' => 'bi-calendar-day', 'color' => 'primary'],
        ['key' => 'bookings_month', 'label' => 'حجوزات الشهر', 'icon' => 'bi-calendar-month', 'color' => 'info'],
        ['key' => 'completed_bookings', 'label' => 'حجوزات مكتملة', 'icon' => 'bi-check2-all', 'color' => 'success'],
        ['key' => 'cancelled_bookings', 'label' => 'حجوزات ملغاة', 'icon' => 'bi-x-octagon', 'color' => 'danger'],
        ['key' => 'monthly_subscriptions', 'label' => 'اشتراكات شهرية', 'icon' => 'bi-calendar-check', 'color' => 'primary'],
        ['key' => 'usage_subscriptions', 'label' => 'اشتراكات حسب الاستخدام', 'icon' => 'bi-graph-up', 'color' => 'warning'],
        ['key' => 'total_wallet_balance', 'label' => 'إجمالي أرصدة المحافظ', 'icon' => 'bi-wallet2', 'color' => 'success', 'format' => 'money'],
    ];
@endphp

@include('partials.report-stat-cards', ['cards' => $cards])
@endsection
