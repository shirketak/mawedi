@extends('layouts.hospital')

@section('page-title', 'تعديل طبيب')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('hospital.doctors.update', $doctor) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">اسم الطبيب *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $doctor->name) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">التخصص *</label>
                    <select name="specialty_id" class="form-select" required>
                        @foreach($specialties as $specialty)
                            <option value="{{ $specialty->id }}" @selected(old('specialty_id', $doctor->specialty_id) == $specialty->id)>{{ $specialty->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">الصورة الشخصية</label>
                    @if($doctor->photo)
                        <div class="mb-2"><img src="{{ \App\Helpers\FileUploader::url($doctor->photo) }}" alt="" class="img-preview rounded-circle"></div>
                    @endif
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">مدة الكشف (بالدقائق) *</label>
                    <input type="number" name="consultation_duration_minutes" class="form-control" value="{{ old('consultation_duration_minutes', $doctor->consultation_duration_minutes) }}" min="5" max="180" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">سعر الكشف (د.ل)</label>
                    <input type="number" name="consultation_price" class="form-control" value="{{ old('consultation_price', $doctor->consultation_price) }}" min="0" step="0.01">
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                <button type="submit" class="btn btn-success">تحديث</button>
                <a href="{{ route('hospital.doctors.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
