@extends('layouts.hospital')

@section('page-title', 'إجازات - ' . $doctor->name)

@section('content')
<div class="row g-3 g-md-4">
    <div class="col-12 col-lg-5 order-2 order-lg-1">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0">إضافة إجازة</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('hospital.doctors.vacations.store', $doctor) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">التاريخ *</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نوع الإجازة *</label>
                        <select name="type" class="form-select" required>
                            @foreach(\App\Enums\VacationType::cases() as $type)
                                <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">السبب</label>
                        <textarea name="reason" class="form-control" rows="2">{{ old('reason') }}</textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="reschedule_bookings" value="1" class="form-check-input" id="reschedule" @checked(old('reschedule_bookings'))>
                        <label class="form-check-label" for="reschedule">تأجيل الحجوزات الموجودة تلقائياً</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">سبب التأجيل (عند تفعيل الخيار أعلاه)</label>
                        <textarea name="reschedule_reason" class="form-control" rows="2">{{ old('reschedule_reason') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-warning text-white w-100">إضافة الإجازة</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-7 order-1 order-lg-2">
        <div class="card">
            <div class="card-header bg-white d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
                <h6 class="mb-0">قائمة الإجازات</h6>
                <a href="{{ route('hospital.doctors.schedule', $doctor) }}" class="btn btn-sm btn-outline-primary">جدول العمل</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>التاريخ</th><th>النوع</th><th class="d-none d-sm-table-cell">السبب</th><th></th></tr></thead>
                    <tbody>
                        @forelse($vacations as $vacation)
                        <tr>
                            <td>{{ $vacation->date->format('Y-m-d') }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $vacation->type->label() }}</span></td>
                            <td class="d-none d-sm-table-cell text-break">{{ $vacation->reason ?? '—' }}</td>
                            <td>
                                <form action="{{ route('hospital.doctors.vacations.destroy', [$doctor, $vacation]) }}" method="POST" onsubmit="return confirm('حذف الإجازة؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">لا توجد إجازات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
