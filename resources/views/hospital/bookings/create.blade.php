@extends('layouts.hospital')

@section('page-title', 'إنشاء حجز')

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
        <h6 class="mb-0">حجز موعد جديد</h6>
        <a href="{{ route('hospital.bookings.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right"></i> العودة للقائمة
        </a>
    </div>
    <div class="card-body">
        @if($doctors->isEmpty())
            <div class="alert alert-warning mb-0">يجب إضافة أطباء نشطين أولاً قبل إنشاء الحجوزات.</div>
        @else
        <form method="POST" action="{{ route('hospital.bookings.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">الطبيب *</label>
                    <select class="form-select" required id="doctorSelect"
                        onchange="window.location='{{ route('hospital.bookings.create') }}?doctor_id='+this.value">
                        <option value="">اختر الطبيب</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected($selectedDoctorId == $doctor->id)>{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">الموعد المتاح *</label>
                    @if($slots->isEmpty())
                        <div class="alert alert-info py-2 mb-0">لا توجد مواعيد متاحة لهذا الطبيب حالياً.</div>
                    @else
                        <select name="doctor_slot_id" class="form-select" required>
                            <option value="">اختر الموعد</option>
                            @foreach($slots as $slot)
                                <option value="{{ $slot->id }}" @selected(old('doctor_slot_id') == $slot->id)>
                                    {{ $slot->date->format('Y-m-d') }} — {{ substr($slot->start_time, 0, 5) }} إلى {{ substr($slot->end_time, 0, 5) }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">اسم المريض *</label>
                    <input type="text" name="patient_name" class="form-control" value="{{ old('patient_name') }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">هاتف المريض *</label>
                    <input type="text" name="patient_phone" class="form-control" value="{{ old('patient_phone') }}" required placeholder="09xxxxxxxx">
                </div>
            </div>
            <div class="mt-4 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-success" @disabled($slots->isEmpty())>
                    <i class="bi bi-plus-circle"></i> إنشاء الحجز
                </button>
                <a href="{{ route('hospital.bookings.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
            <p class="text-muted small mt-3 mb-0">
                <i class="bi bi-info-circle"></i>
                يُنشأ الحجز بحالة «قيد الانتظار». يمكنك تأكيده لاحقاً من قائمة الحجوزات.
            </p>
        </form>
        @endif
    </div>
</div>
@endsection
