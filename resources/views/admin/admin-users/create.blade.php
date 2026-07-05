@extends('layouts.admin')

@section('page-title', 'إضافة مستخدم إدارة')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.admin-users.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">الاسم *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">البريد الإلكتروني *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">كلمة المرور *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">تأكيد كلمة المرور *</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">الدور *</label>
                    <select name="role" class="form-select" required>
                        <option value="">اختر الدور</option>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 d-flex align-items-end">
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', true))>
                        <label class="form-check-label" for="is_active">حساب مفعّل</label>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                <button type="submit" class="btn btn-primary">حفظ</button>
                <a href="{{ route('admin.admin-users.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
