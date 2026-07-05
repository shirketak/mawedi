@extends('layouts.admin')

@section('page-title', 'الإحصائيات المالية')

@section('content')
@include('partials.report-nav')

@php
    $cards = [
        ['key' => 'monthly_subscriptions', 'label' => 'اشتراكات شهرية', 'icon' => 'bi-calendar-check', 'color' => 'primary'],
        ['key' => 'usage_subscriptions', 'label' => 'اشتراكات حسب الاستخدام', 'icon' => 'bi-graph-up', 'color' => 'warning'],
        ['key' => 'total_wallet_balance', 'label' => 'إجمالي أرصدة المحافظ', 'icon' => 'bi-wallet2', 'color' => 'success', 'format' => 'money'],
    ];
@endphp

@include('partials.report-stat-cards', ['cards' => $cards])
@endsection
