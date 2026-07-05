@extends('layouts.admin')

@section('page-title', 'إحصائيات المستشفيات')

@section('content')
@include('partials.report-nav')

@php
    $cards = [
        ['key' => 'hospitals', 'label' => 'إجمالي المستشفيات', 'icon' => 'bi-building', 'color' => 'primary'],
        ['key' => 'active_hospitals', 'label' => 'مستشفيات مفعّلة', 'icon' => 'bi-check-circle', 'color' => 'success'],
        ['key' => 'inactive_hospitals', 'label' => 'مستشفيات موقوفة', 'icon' => 'bi-x-circle', 'color' => 'secondary'],
        ['key' => 'doctors', 'label' => 'الأطباء', 'icon' => 'bi-person-badge', 'color' => 'info'],
        ['key' => 'specialties', 'label' => 'التخصصات', 'icon' => 'bi-heart-pulse', 'color' => 'warning'],
    ];
@endphp

@include('partials.report-stat-cards', ['cards' => $cards])
@endsection
