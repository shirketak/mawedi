@extends('layouts.admin')

@section('page-title', 'تعديل مستخدم')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.patients.update', $patient) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">الاسم *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $patient->name) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">رقم الهاتف *</label>
                    <input type="text" name="phone" class="form-control" dir="ltr" value="{{ old('phone', $patient->phone) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $patient->email) }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">الحالة *</label>
                    <select name="is_active" class="form-select" required>
                        <option value="1" @selected(old('is_active', $patient->is_active ? '1' : '0') == '1')>مفعّل</option>
                        <option value="0" @selected(old('is_active', $patient->is_active ? '1' : '0') == '0')>موقوف</option>
                    </select>
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                <button type="submit" class="btn btn-primary">تحديث</button>
                <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
