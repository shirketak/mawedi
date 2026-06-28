@extends('layouts.hospital')

@section('page-title', 'جدول عمل - ' . $doctor->name)

@section('content')
<div class="row g-3 g-md-4">
    <div class="col-12 col-lg-8 order-2 order-lg-1">
        <div class="card">
            <div class="card-header bg-white d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
                <h6 class="mb-0">جدول العمل الأسبوعي</h6>
                <span class="badge bg-primary align-self-sm-center">مدة الكشف: {{ $doctor->consultation_duration_minutes }} دقيقة</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('hospital.doctors.schedule.update', $doctor) }}" id="scheduleForm">
                    @csrf @method('PUT')
                    @php
                        $scheduleMap = collect($schedule)->keyBy(fn($d) => $d['day']->value);
                    @endphp
                    @foreach($days as $dayValue => $dayLabel)
                        @php
                            $daySchedule = $scheduleMap->get($dayValue);
                            $periods = $daySchedule['periods'] ?? collect();
                        @endphp
                        <div class="border rounded p-3 mb-3 day-block" data-day="{{ $dayValue }}">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 mb-2 day-block-header">
                                <strong>{{ $dayLabel }}</strong>
                                <button type="button" class="btn btn-sm btn-outline-primary add-period w-100 w-sm-auto"><i class="bi bi-plus"></i> إضافة فترة</button>
                            </div>
                            <input type="hidden" name="schedule[{{ $dayValue }}][day_of_week]" value="{{ $dayValue }}">
                            <div class="periods-container">
                                @forelse($periods as $idx => $period)
                                <div class="row g-2 mb-2 period-row">
                                    <div class="col-12 col-sm-5">
                                        <label class="form-label small">من</label>
                                        <input type="time" name="schedule[{{ $dayValue }}][periods][{{ $idx }}][start_time]" class="form-control" value="{{ $period['start_time'] }}" required>
                                    </div>
                                    <div class="col-12 col-sm-5">
                                        <label class="form-label small">إلى</label>
                                        <input type="time" name="schedule[{{ $dayValue }}][periods][{{ $idx }}][end_time]" class="form-control" value="{{ $period['end_time'] }}" required>
                                    </div>
                                    <div class="col-12 col-sm-2 d-flex align-items-sm-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-period w-100"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                                @empty
                                <p class="text-muted small mb-0 no-periods">لا توجد فترات عمل</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> حفظ الجدول وإنشاء المواعيد</button>
                        <a href="{{ route('hospital.doctors.index') }}" class="btn btn-outline-secondary">رجوع</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4 order-1 order-lg-2">
        <div class="card mb-3 mb-lg-4">
            <div class="card-header bg-white"><h6 class="mb-0">تأجيل حجوزات يوم</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('hospital.doctors.postpone', $doctor) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">التاريخ</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">سبب التأجيل</label>
                        <textarea name="reason" class="form-control" rows="2" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 text-white" onclick="return confirm('سيتم نقل جميع حجوزات هذا اليوم لأقرب مواعيد متاحة. متابعة؟')">
                        <i class="bi bi-arrow-repeat"></i> تأجيل الحجوزات
                    </button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h6>روابط سريعة</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('hospital.doctors.vacations', $doctor) }}" class="btn btn-outline-warning btn-sm"><i class="bi bi-calendar-x"></i> إدارة الإجازات</a>
                    <a href="{{ route('hospital.doctors.edit', $doctor) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i> تعديل بيانات الطبيب</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.add-period').forEach(btn => {
    btn.addEventListener('click', function() {
        const block = this.closest('.day-block');
        const day = block.dataset.day;
        const container = block.querySelector('.periods-container');
        const noMsg = container.querySelector('.no-periods');
        if (noMsg) noMsg.remove();
        const idx = container.querySelectorAll('.period-row').length;
        const html = `
            <div class="row g-2 mb-2 period-row">
                <div class="col-12 col-sm-5"><label class="form-label small">من</label><input type="time" name="schedule[${day}][periods][${idx}][start_time]" class="form-control" required></div>
                <div class="col-12 col-sm-5"><label class="form-label small">إلى</label><input type="time" name="schedule[${day}][periods][${idx}][end_time]" class="form-control" required></div>
                <div class="col-12 col-sm-2 d-flex align-items-sm-end"><button type="button" class="btn btn-outline-danger btn-sm remove-period w-100"><i class="bi bi-trash"></i></button></div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
    });
});
document.addEventListener('click', e => {
    if (e.target.closest('.remove-period')) {
        const row = e.target.closest('.period-row');
        const container = row.parentElement;
        row.remove();
        if (!container.querySelector('.period-row')) {
            container.innerHTML = '<p class="text-muted small mb-0 no-periods">لا توجد فترات عمل</p>';
        }
    }
});
</script>
@endpush
