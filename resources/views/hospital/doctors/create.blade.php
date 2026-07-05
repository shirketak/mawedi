@extends('layouts.hospital')

@section('page-title', 'إضافة طبيب')

@section('content')
<div class="card">
    <div class="card-body">
        @if($specialties->isEmpty())
            <div class="alert alert-warning">يجب إضافة تخصصات للمستشفى أولاً قبل إضافة الأطباء.</div>
            <a href="{{ route('hospital.specialties.index') }}" class="btn btn-primary w-100 w-sm-auto">إدارة التخصصات</a>
        @else
        <form method="POST" action="{{ route('hospital.doctors.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">اسم الطبيب *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">التخصص *</label>
                    <select name="specialty_id" class="form-select" required>
                        <option value="">اختر التخصص</option>
                        @foreach($specialties as $specialty)
                            <option value="{{ $specialty->id }}" @selected(old('specialty_id') == $specialty->id)>{{ $specialty->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">الصورة الشخصية</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">مدة الكشف (بالدقائق) *</label>
                    <input type="number" name="consultation_duration_minutes" class="form-control" value="{{ old('consultation_duration_minutes', 20) }}" min="5" max="180" required>
                    <small class="text-muted">مثال: 15، 20، 30، 45 دقيقة</small>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">سعر الكشف (د.ل)</label>
                    <input type="number" name="consultation_price" class="form-control" value="{{ old('consultation_price', 0) }}" min="0" step="0.01">
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                <button type="submit" class="btn btn-success">حفظ</button>
                <a href="{{ route('hospital.doctors.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection
