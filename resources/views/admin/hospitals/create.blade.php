@extends('layouts.admin')

@section('page-title', 'إضافة مستشفى')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.hospitals.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">اسم المستشفى *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">المحافظة *</label>
                    <select name="governorate" class="form-select" required>
                        <option value="">اختر المحافظة</option>
                        @foreach($governorates as $value => $label)
                            <option value="{{ $value }}" @selected(old('governorate') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">الشعار</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">رابط الموقع على الخريطة</label>
                    <input type="url" name="map_url" class="form-control" value="{{ old('map_url') }}">
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">رقم الهاتف *</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">رقم هاتف إضافي</label>
                    <input type="text" name="phone_secondary" class="form-control" value="{{ old('phone_secondary') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">البريد الإلكتروني *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">الموقع الإلكتروني</label>
                    <input type="url" name="website" class="form-control" value="{{ old('website') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">العنوان *</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}" required>
                </div>

                <div class="col-12"><hr class="my-2"><h6>بيانات حساب المستشفى</h6></div>
                <div class="col-12 col-md-6">
                    <label class="form-label">بريد تسجيل الدخول *</label>
                    <input type="email" name="user_email" class="form-control" value="{{ old('user_email') }}" required>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label">كلمة المرور *</label>
                    <input type="password" name="user_password" class="form-control" required>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label">تأكيد كلمة المرور *</label>
                    <input type="password" name="user_password_confirmation" class="form-control" required>
                </div>

                @include('partials.hospital-subscription-fields')
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                <button type="submit" class="btn btn-primary">حفظ</button>
                <a href="{{ route('admin.hospitals.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
