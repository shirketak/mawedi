@extends('layouts.admin')

@section('page-title', 'سعر الكشف')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 mb-3 mb-md-4">
    <div class="min-w-0">
        <h5 class="mb-1 text-break">{{ $doctor->name }}</h5>
        <div class="text-muted small">
            {{ $doctor->hospital?->name }} · {{ $doctor->specialty?->name }}
        </div>
    </div>
    <a href="{{ route('admin.hospitals.show', $doctor->hospital) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-right"></i> العودة
    </a>
</div>

<div class="row g-3 g-md-4">
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white"><h6 class="mb-0">تعديل سعر الكشف</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.doctors.price.update', $doctor) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">السعر الحالي</label>
                        <div class="h4 text-primary">{{ number_format($doctor->consultation_price ?? 0, 2) }} د.ل</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">سعر الكشف الجديد *</label>
                        <input type="number" name="consultation_price" class="form-control" step="0.01" min="0"
                            value="{{ old('consultation_price', $doctor->consultation_price) }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">حفظ السعر</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white"><h6 class="mb-0">سجل تغييرات الأسعار</h6></div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-scroll">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>السعر السابق</th>
                            <th>السعر الجديد</th>
                            <th class="d-none d-md-table-cell">بواسطة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($doctor->priceLogs->sortByDesc('created_at') as $log)
                        <tr>
                            <td class="small">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ number_format($log->old_price ?? 0, 2) }} د.ل</td>
                            <td>{{ number_format($log->new_price, 2) }} د.ل</td>
                            <td class="d-none d-md-table-cell">{{ $log->changedBy?->name ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">لا يوجد سجل تغييرات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
