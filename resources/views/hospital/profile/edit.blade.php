@extends('layouts.hospital')

@section('page-title', 'بيانات المستشفى')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('hospital.profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">اسم المستشفى</label>
                    <input type="text" class="form-control" value="{{ $hospital->name }}" disabled>
                    <small class="text-muted">يتم تعديل الاسم من لوحة الإدارة فقط</small>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">المحافظة</label>
                    <input type="text" class="form-control" value="{{ $hospital->governorateLabel() }}" disabled>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">الشعار</label>
                    @if($hospital->logo)
                        <div class="mb-2"><img src="{{ \App\Helpers\FileUploader::url($hospital->logo) }}" alt="" class="img-preview"></div>
                    @endif
                    <input type="file" name="logo" class="form-control" accept="image/*">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">رابط الموقع على الخريطة</label>
                    <input type="url" name="map_url" class="form-control" value="{{ old('map_url', $hospital->map_url) }}">
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">رقم الهاتف *</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $hospital->phone) }}" required>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">رقم هاتف إضافي</label>
                    <input type="text" name="phone_secondary" class="form-control" value="{{ old('phone_secondary', $hospital->phone_secondary) }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">البريد الإلكتروني *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $hospital->email) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">الموقع الإلكتروني</label>
                    <input type="url" name="website" class="form-control" value="{{ old('website', $hospital->website) }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">العنوان *</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $hospital->address) }}" required>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-success w-100 w-sm-auto">حفظ التغييرات</button>
            </div>
        </form>
    </div>
</div>
@endsection
