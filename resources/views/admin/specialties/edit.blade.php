@extends('layouts.admin')

@section('page-title', 'تعديل تخصص')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.specialties.update', $specialty) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">اسم التخصص *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $specialty->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">أيقونة / صورة التخصص</label>
                @if($specialty->icon)
                    <div class="mb-2"><img src="{{ \App\Helpers\FileUploader::url($specialty->icon) }}" alt="" class="img-preview"></div>
                @endif
                <input type="file" name="icon" class="form-control" accept="image/*">
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <button type="submit" class="btn btn-primary">تحديث</button>
                <a href="{{ route('admin.specialties.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
