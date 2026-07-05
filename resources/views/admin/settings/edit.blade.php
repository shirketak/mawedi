@extends('layouts.admin')

@section('page-title', 'إعدادات النظام')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf @method('PUT')

            <h6 class="text-muted mb-3">معلومات التطبيق</h6>
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label">اسم التطبيق *</label>
                    <input type="text" name="app_name" class="form-control" value="{{ old('app_name', $settings['app_name'] ?? '') }}" required>
                </div>
            </div>

            <h6 class="text-muted mb-3">معلومات التواصل</h6>
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="contact_address" class="form-control" value="{{ old('contact_address', $settings['contact_address'] ?? '') }}">
                </div>
            </div>

            <h6 class="text-muted mb-3">وسائل التواصل الاجتماعي</h6>
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <label class="form-label">فيسبوك</label>
                    <input type="url" name="social_facebook" class="form-control" dir="ltr" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">تويتر / X</label>
                    <input type="url" name="social_twitter" class="form-control" dir="ltr" value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">إنستغرام</label>
                    <input type="url" name="social_instagram" class="form-control" dir="ltr" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}">
                </div>
            </div>

            <h6 class="text-muted mb-3">الاشتراكات الافتراضية</h6>
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <label class="form-label">سعر الاشتراك الشهري الافتراضي *</label>
                    <input type="number" name="default_monthly_price" class="form-control" step="0.01" min="0"
                        value="{{ old('default_monthly_price', $settings['default_monthly_price'] ?? '') }}" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">رسوم الحجز الافتراضية *</label>
                    <input type="number" name="default_usage_fee_per_booking" class="form-control" step="0.01" min="0"
                        value="{{ old('default_usage_fee_per_booking', $settings['default_usage_fee_per_booking'] ?? '') }}" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">أيام الفترة المجانية الافتراضية *</label>
                    <input type="number" name="default_free_trial_days" class="form-control" min="0" max="365"
                        value="{{ old('default_free_trial_days', $settings['default_free_trial_days'] ?? '') }}" required>
                </div>
            </div>

            <h6 class="text-muted mb-3">السياسات</h6>
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label">سياسة الخصوصية</label>
                    <textarea name="privacy_policy" class="form-control" rows="6">{{ old('privacy_policy', $settings['privacy_policy'] ?? '') }}</textarea>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">شروط الاستخدام</label>
                    <textarea name="terms_of_use" class="form-control" rows="6">{{ old('terms_of_use', $settings['terms_of_use'] ?? '') }}</textarea>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2">
                <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
            </div>
        </form>
    </div>
</div>
@endsection
