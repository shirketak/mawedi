@extends('layouts.hospital')

@section('page-title', 'الحجوزات')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 mb-3">
    <div></div>
    <a href="{{ route('hospital.bookings.create') }}" class="btn btn-success btn-sm">
        <i class="bi bi-plus-circle"></i> حجز جديد
    </a>
</div>

<div class="card mb-3 mb-md-4">
    <div class="card-body">
        <form method="GET" class="row g-3 filter-form">
            <div class="col-12 col-sm-6 col-lg-3">
                <input type="text" name="search" class="form-control" placeholder="بحث بالمريض..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <select name="doctor_id" class="form-select">
                    <option value="">كل الأطباء</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected(($filters['doctor_id'] ?? '') == $doctor->id)>{{ $doctor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <select name="payment_status" class="form-select">
                    <option value="">حالة الدفع</option>
                    @foreach($paymentStatuses as $ps)
                        <option value="{{ $ps->value }}" @selected(($filters['payment_status'] ?? '') === $ps->value)>{{ $ps->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label small d-lg-none">من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label small d-lg-none">إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <button type="submit" class="btn btn-primary w-100">بحث</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white"><h6 class="mb-0">قائمة الحجوزات ({{ $bookings->total() }})</h6></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 table-scroll">
            <thead>
                <tr>
                    <th>المريض</th><th class="d-none d-md-table-cell">الهاتف</th><th>الطبيب</th>
                    <th class="d-none d-lg-table-cell">التخصص</th><th>التاريخ</th><th>الوقت</th>
                    <th>الحالة</th><th class="d-none d-sm-table-cell">الدفع</th><th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td class="text-break">{{ $booking->patient_name }}</td>
                    <td class="d-none d-md-table-cell">{{ $booking->patient_phone }}</td>
                    <td class="text-break">{{ $booking->doctor->name }}</td>
                    <td class="d-none d-lg-table-cell">{{ $booking->specialty->name }}</td>
                    <td>{{ $booking->booking_date->format('Y-m-d') }}</td>
                    <td>{{ substr($booking->booking_time, 0, 5) }}</td>
                    <td><span class="badge {{ $booking->status->badgeClass() }}">{{ $booking->status->label() }}</span></td>
                    <td class="d-none d-sm-table-cell"><span class="badge {{ $booking->payment_status->badgeClass() }}">{{ $booking->payment_status->label() }}</span></td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                إجراء
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if($booking->status === \App\Enums\BookingStatus::Pending)
                                    <li>
                                        <form method="POST" action="{{ route('hospital.bookings.confirm', $booking) }}" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="dropdown-item text-primary">تأكيد</button>
                                        </form>
                                    </li>
                                @endif
                                @if($booking->status === \App\Enums\BookingStatus::Confirmed)
                                    <li>
                                        <form method="POST" action="{{ route('hospital.bookings.complete', $booking) }}" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="dropdown-item text-success">إكمال</button>
                                        </form>
                                    </li>
                                @endif
                                @if(in_array($booking->status, [\App\Enums\BookingStatus::Pending, \App\Enums\BookingStatus::Confirmed], true))
                                    <li>
                                        <form method="POST" action="{{ route('hospital.bookings.no-show', $booking) }}" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="dropdown-item">لم يحضر</button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('hospital.bookings.cancel', $booking) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الحجز؟')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="dropdown-item text-danger">إلغاء</button>
                                        </form>
                                    </li>
                                @endif
                                @if($booking->payment_status === \App\Enums\PaymentStatus::Unpaid && !in_array($booking->status, [\App\Enums\BookingStatus::Cancelled], true))
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('hospital.bookings.mark-paid', $booking) }}" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="dropdown-item">تسجيل الدفع</button>
                                        </form>
                                    </li>
                                @endif
                                @if(!in_array($booking->status, [\App\Enums\BookingStatus::Pending, \App\Enums\BookingStatus::Confirmed], true) && $booking->payment_status !== \App\Enums\PaymentStatus::Unpaid)
                                    <li><span class="dropdown-item text-muted disabled">لا إجراءات</span></li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">لا توجد حجوزات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())<div class="card-footer">{{ $bookings->links() }}</div>@endif
</div>
@endsection
