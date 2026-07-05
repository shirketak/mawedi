@extends('layouts.admin')

@section('page-title', 'ملف المستخدم')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 mb-3 mb-md-4">
    <div class="min-w-0">
        <h5 class="mb-1 text-break">{{ $patient->name }}</h5>
        <div class="text-muted small" dir="ltr">{{ $patient->phone }} @if($patient->email) · {{ $patient->email }} @endif</div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <span class="badge {{ $patient->is_active ? 'badge-active' : 'badge-inactive' }} align-self-center">
            {{ $patient->is_active ? 'مفعّل' : 'موقوف' }}
        </span>
        <a href="{{ route('admin.patients.edit', $patient) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i> تعديل</a>
        <a href="{{ route('admin.patients.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-right"></i> العودة</a>
    </div>
</div>

@php
    $tabs = [
        'completed' => ['label' => 'مكتملة', 'icon' => 'bi-check2-all', 'count' => $bookingGroups['completed']->count()],
        'cancelled' => ['label' => 'ملغاة', 'icon' => 'bi-x-octagon', 'count' => $bookingGroups['cancelled']->count()],
        'upcoming' => ['label' => 'قادمة', 'icon' => 'bi-calendar-event', 'count' => $bookingGroups['upcoming']->count()],
        'future' => ['label' => 'مستقبلية', 'icon' => 'bi-calendar-plus', 'count' => $bookingGroups['future']->count()],
        'no_show' => ['label' => 'لم يحضر', 'icon' => 'bi-person-x', 'count' => $bookingGroups['no_show']->count()],
        'missed' => ['label' => 'فائتة', 'icon' => 'bi-exclamation-triangle', 'count' => $bookingGroups['missed']->count()],
    ];
@endphp

<ul class="nav nav-tabs flex-nowrap overflow-auto mb-3" id="bookingTabs" role="tablist">
    @foreach($tabs as $key => $tab)
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $key }}" data-bs-toggle="tab"
            data-bs-target="#pane-{{ $key }}" type="button" role="tab">
            <i class="bi {{ $tab['icon'] }}"></i>
            <span class="d-none d-sm-inline">{{ $tab['label'] }}</span>
            <span class="badge bg-secondary ms-1">{{ $tab['count'] }}</span>
        </button>
    </li>
    @endforeach
</ul>

<div class="tab-content" id="bookingTabsContent">
    @foreach($tabs as $key => $tab)
    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pane-{{ $key }}" role="tabpanel">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-scroll">
                    <thead>
                        <tr>
                            <th>المستشفى</th>
                            <th class="d-none d-md-table-cell">الطبيب</th>
                            <th class="d-none d-lg-table-cell">التخصص</th>
                            <th>التاريخ</th>
                            <th class="d-none d-sm-table-cell">الوقت</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookingGroups[$key] as $booking)
                        <tr>
                            <td class="text-break">{{ $booking->hospital?->name ?? '—' }}</td>
                            <td class="d-none d-md-table-cell">{{ $booking->doctor?->name ?? '—' }}</td>
                            <td class="d-none d-lg-table-cell">{{ $booking->specialty?->name ?? '—' }}</td>
                            <td>{{ $booking->booking_date?->format('Y-m-d') }}</td>
                            <td class="d-none d-sm-table-cell">{{ $booking->booking_time ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $booking->status?->badgeClass() ?? 'bg-secondary' }}">
                                    {{ $booking->status?->label() ?? '—' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">لا توجد حجوزات في هذه الفئة</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
