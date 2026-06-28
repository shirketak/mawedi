@extends('layouts.hospital')

@section('page-title', 'لوحة التحكم')

@section('content')
<div class="welcome-banner mb-3 mb-md-4">
    <div class="row align-items-center g-3">
        <div class="col-12 col-md-8">
            <h4 class="mb-2">مرحباً، {{ $hospital->name }}</h4>
            <p class="mb-0 opacity-75">إدارة الأطباء والحجوزات والمواعيد من مكان واحد</p>
        </div>
        <div class="col-12 col-md-4 text-md-end">
            @if($hospital->logo)
                <img src="{{ \App\Helpers\FileUploader::url($hospital->logo) }}" alt="" class="rounded" style="width:clamp(3.5rem,12vw,5rem);height:clamp(3.5rem,12vw,5rem);object-fit:cover;border:3px solid rgba(255,255,255,.3)">
            @endif
        </div>
    </div>
</div>

<div class="row g-3 g-md-4 mb-3 mb-md-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card h-100">
            <div class="card-body text-center py-3">
                <div class="icon bg-success bg-opacity-10 text-success mx-auto mb-2"><i class="bi bi-person-badge"></i></div>
                <h5 class="mb-0">{{ $stats['doctors'] }}</h5>
                <small class="text-muted">الأطباء</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card h-100">
            <div class="card-body text-center py-3">
                <div class="icon bg-primary bg-opacity-10 text-primary mx-auto mb-2"><i class="bi bi-heart-pulse"></i></div>
                <h5 class="mb-0">{{ $stats['specialties'] }}</h5>
                <small class="text-muted">التخصصات</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card h-100">
            <div class="card-body text-center py-3">
                <div class="icon bg-warning bg-opacity-10 text-warning mx-auto mb-2"><i class="bi bi-calendar-day"></i></div>
                <h5 class="mb-0">{{ $stats['bookings_today'] }}</h5>
                <small class="text-muted">حجوزات اليوم</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center py-3">
                <div class="icon bg-info bg-opacity-10 text-info mx-auto mb-2"><i class="bi bi-hourglass-split"></i></div>
                <h5 class="mb-0">{{ $stats['pending_bookings'] }}</h5>
                <small class="text-muted">قيد الانتظار</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center py-3">
                <div class="icon bg-danger bg-opacity-10 text-danger mx-auto mb-2"><i class="bi bi-credit-card"></i></div>
                <h5 class="mb-0">{{ $stats['unpaid_bookings'] }}</h5>
                <small class="text-muted">غير مدفوعة</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 g-md-4">
    <div class="col-12 col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-white d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
                <h6 class="mb-0">الحجوزات القادمة</h6>
                <a href="{{ route('hospital.bookings.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-scroll">
                    <thead><tr><th>المريض</th><th class="d-none d-sm-table-cell">الطبيب</th><th>التاريخ</th><th>الوقت</th><th>الحالة</th></tr></thead>
                    <tbody>
                        @forelse($upcomingBookings as $booking)
                        <tr>
                            <td class="text-break">{{ $booking->patient_name }}</td>
                            <td class="d-none d-sm-table-cell">{{ $booking->doctor->name }}</td>
                            <td>{{ $booking->booking_date->format('Y-m-d') }}</td>
                            <td>{{ substr($booking->booking_time, 0, 5) }}</td>
                            <td><span class="badge {{ $booking->status->badgeClass() }}">{{ $booking->status->label() }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">لا توجد حجوزات قادمة</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-white d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
                <h6 class="mb-0">أحدث الأطباء</h6>
                <a href="{{ route('hospital.doctors.create') }}" class="btn btn-sm btn-success"><i class="bi bi-plus"></i> إضافة</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($recentDoctors as $doctor)
                <li class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
                    <div class="min-w-0">
                        <strong class="text-break">{{ $doctor->name }}</strong>
                        <br><small class="text-muted">{{ $doctor->specialty->name }}</small>
                    </div>
                    <a href="{{ route('hospital.doctors.schedule', $doctor) }}" class="btn btn-sm btn-outline-primary flex-shrink-0">الجدول</a>
                </li>
                @empty
                <li class="list-group-item text-center text-muted">لا يوجد أطباء بعد</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
