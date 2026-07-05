@extends('layouts.admin')

@section('page-title', 'إحصائيات المستشفى')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 mb-3 mb-md-4">
    <div class="d-flex align-items-center gap-3 min-w-0">
        @if($hospital->logo)
            <img src="{{ \App\Helpers\FileUploader::url($hospital->logo) }}" alt="" class="img-preview rounded">
        @endif
        <div class="min-w-0">
            <h5 class="mb-1 text-break">{{ $hospital->name }}</h5>
            <div class="text-muted small">{{ $hospital->governorateLabel() }} · {{ $hospital->phone }}</div>
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.hospitals.wallet', $hospital) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-wallet2"></i> المحفظة
        </a>
        <a href="{{ route('admin.hospitals.edit', $hospital) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil"></i> تعديل
        </a>
        <a href="{{ route('admin.hospitals.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i> العودة
        </a>
        @unless($hospital->is_active)
        <form action="{{ route('admin.hospitals.activate', $hospital) }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-circle"></i> تفعيل المستشفى</button>
        </form>
        @endunless
    </div>
</div>

@php
    $statCards = [
        ['key' => 'total_bookings', 'label' => 'إجمالي الحجوزات', 'icon' => 'bi-calendar-check', 'color' => 'primary'],
        ['key' => 'completed_bookings', 'label' => 'حجوزات مكتملة', 'icon' => 'bi-check2-all', 'color' => 'success'],
        ['key' => 'cancelled_bookings', 'label' => 'حجوزات ملغاة', 'icon' => 'bi-x-octagon', 'color' => 'danger'],
        ['key' => 'upcoming_bookings', 'label' => 'حجوزات قادمة', 'icon' => 'bi-hourglass-split', 'color' => 'info'],
        ['key' => 'doctors_count', 'label' => 'الأطباء', 'icon' => 'bi-person-badge', 'color' => 'success'],
        ['key' => 'specialties_count', 'label' => 'التخصصات', 'icon' => 'bi-heart-pulse', 'color' => 'warning'],
        ['key' => 'total_revenue', 'label' => 'إجمالي الإيرادات', 'icon' => 'bi-cash-stack', 'color' => 'primary', 'format' => 'money'],
        ['key' => 'wallet_balance', 'label' => 'رصيد المحفظة', 'icon' => 'bi-wallet2', 'color' => 'success', 'format' => 'money'],
    ];
@endphp

<div class="row g-3 g-md-4 mb-4">
    @foreach($statCards as $card)
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <div class="icon bg-{{ $card['color'] }} bg-opacity-10 text-{{ $card['color'] }}">
                    <i class="bi {{ $card['icon'] }}"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted small">{{ $card['label'] }}</div>
                    <h4 class="mb-0">
                        @if(($card['format'] ?? '') === 'money')
                            {{ number_format($stats[$card['key']], 2) }} د.ل
                        @else
                            {{ number_format($stats[$card['key']]) }}
                        @endif
                    </h4>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3 g-md-4">
    <div class="col-12 col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0">إدارة الاشتراك</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge {{ $hospital->subscription_status?->badgeClass() ?? 'bg-secondary' }}">
                        {{ $hospital->subscriptionStatusLabel() }}
                    </span>
                    <span class="text-muted ms-2">{{ $hospital->subscriptionTypeLabel() }}</span>
                </div>
                <form method="POST" action="{{ route('admin.hospitals.subscription.update', $hospital) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">نوع الاشتراك *</label>
                            <select name="subscription_type" class="form-select" required>
                                @foreach($subscriptionTypes as $value => $label)
                                    <option value="{{ $value }}" @selected(old('subscription_type', $hospital->subscription_type?->value) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">سعر الاشتراك الشهري</label>
                            <input type="number" name="monthly_price" class="form-control" step="0.01" min="0"
                                value="{{ old('monthly_price', $hospital->monthly_price) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">رسوم الحجز (حسب الاستخدام)</label>
                            <input type="number" name="usage_fee_per_booking" class="form-control" step="0.01" min="0"
                                value="{{ old('usage_fee_per_booking', $hospital->usage_fee_per_booking) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">تاريخ بداية الاشتراك</label>
                            <input type="date" name="subscription_starts_at" class="form-control"
                                value="{{ old('subscription_starts_at', $hospital->subscription_starts_at?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">مدة الاشتراك بالأشهر</label>
                            <input type="number" name="subscription_duration_months" class="form-control" min="1" max="36"
                                value="{{ old('subscription_duration_months') }}" placeholder="مثال: 12">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">تاريخ انتهاء الاشتراك</label>
                            <input type="date" name="subscription_ends_at" class="form-control"
                                value="{{ old('subscription_ends_at', $hospital->subscription_ends_at?->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">حفظ الاشتراك</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0">منح فترة مجانية</h6>
            </div>
            <div class="card-body">
                @if($hospital->trial_ends_at)
                    <p class="text-muted small mb-3">
                        الفترة المجانية الحالية تنتهي في: <strong>{{ $hospital->trial_ends_at->format('Y-m-d') }}</strong>
                    </p>
                @endif
                <form method="POST" action="{{ route('admin.hospitals.free-trial', $hospital) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">عدد الأيام *</label>
                        <input type="number" name="days" class="form-control" min="1" max="365"
                            value="{{ old('days', $hospital->free_trial_days ?? 14) }}" required>
                    </div>
                    <button type="submit" class="btn btn-warning">منح الفترة المجانية</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
