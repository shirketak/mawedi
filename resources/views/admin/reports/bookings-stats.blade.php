@extends('layouts.admin')

@section('page-title', 'إحصائيات الحجوزات')

@section('content')
@include('partials.report-nav')

@php
    $cards = [
        ['key' => 'bookings_today', 'label' => 'حجوزات اليوم', 'icon' => 'bi-calendar-day', 'color' => 'primary'],
        ['key' => 'bookings_month', 'label' => 'حجوزات هذا الشهر', 'icon' => 'bi-calendar-month', 'color' => 'info'],
        ['key' => 'completed_bookings', 'label' => 'حجوزات مكتملة', 'icon' => 'bi-check2-all', 'color' => 'success'],
        ['key' => 'cancelled_bookings', 'label' => 'حجوزات ملغاة', 'icon' => 'bi-x-octagon', 'color' => 'danger'],
    ];
@endphp

@include('partials.report-stat-cards', ['cards' => $cards])
@endsection
