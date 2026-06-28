@extends('layouts.admin')

@section('page-title', 'إضافة تخصص')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.specialties.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">اسم التخصص *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">أيقونة / صورة التخصص</label>
                <input type="file" name="icon" class="form-control" accept="image/*">
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <button type="submit" class="btn btn-primary">حفظ</button>
                <a href="{{ route('admin.specialties.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
