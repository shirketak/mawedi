@extends('layouts.guest')

@section('auth-title', 'لوحة الإدارة')
@section('auth-subtitle', 'تسجيل الدخول للإدارة')

@section('content')
<form method="POST" action="{{ route('admin.login') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">البريد الإلكتروني</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
    </div>
    <div class="mb-3">
        <label class="form-label">كلمة المرور</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label" for="remember">تذكرني</label>
    </div>
    <button type="submit" class="btn btn-primary w-100">دخول</button>
</form>
@endsection
