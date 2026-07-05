@extends('layouts.admin')

@section('page-title', 'تعديل مستشفى')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.hospitals.update', $hospital) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">اسم المستشفى *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $hospital->name) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">المحافظة *</label>
                    <select name="governorate" class="form-select" required>
                        @foreach($governorates as $value => $label)
                            <option value="{{ $value }}" @selected(old('governorate', $hospital->governorate?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
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

                @include('partials.hospital-subscription-fields', ['hospital' => $hospital])
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                <button type="submit" class="btn btn-primary">تحديث</button>
                <a href="{{ route('admin.hospitals.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
