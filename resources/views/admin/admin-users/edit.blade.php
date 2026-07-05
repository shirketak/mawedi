@extends('layouts.admin')

@section('page-title', 'تعديل مستخدم إدارة')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.admin-users.update', $admin_user) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">الاسم *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $admin_user->name) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">البريد الإلكتروني *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $admin_user->email) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">كلمة المرور الجديدة</label>
                    <input type="password" name="password" class="form-control" placeholder="اتركه فارغاً إن لم تُرد التغيير">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">الدور *</label>
                    <select name="role" class="form-select" required @disabled($admin_user->id === auth('admin')->id())>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', $admin_user->role?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($admin_user->id === auth('admin')->id())
                        <input type="hidden" name="role" value="{{ $admin_user->role?->value }}">
                        <div class="form-text">لا يمكنك تغيير دورك الحالي.</div>
                    @endif
                </div>
                <div class="col-12 col-md-6 d-flex align-items-end">
                    @if($admin_user->id === auth('admin')->id())
                        <input type="hidden" name="is_active" value="1">
                        <span class="text-muted small">لا يمكنك إيقاف حسابك الحالي.</span>
                    @else
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                                @checked(old('is_active', $admin_user->is_active))>
                            <label class="form-check-label" for="is_active">حساب مفعّل</label>
                        </div>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                <button type="submit" class="btn btn-primary">تحديث</button>
                <a href="{{ route('admin.admin-users.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
