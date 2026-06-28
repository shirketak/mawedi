@extends('layouts.admin')

@section('page-title', 'إدارة التخصصات الطبية')

@section('content')
<div class="card mb-3 mb-md-4">
    <div class="card-body">
        <form method="GET" class="row g-3 filter-form">
            <div class="col-12 col-sm-6 col-lg-4">
                <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <select name="is_active" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="1" @selected(request('is_active') === '1')>مفعّل</option>
                    <option value="0" @selected(request('is_active') === '0')>موقوف</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">بحث</button>
                    <a href="{{ route('admin.specialties.index') }}" class="btn btn-outline-secondary">إعادة</a>
                </div>
            </div>
        </form>
    </div>
</div>

@include('partials.page-toolbar', [
    'title' => 'قائمة التخصصات (' . $specialties->total() . ')',
    'actionUrl' => route('admin.specialties.create'),
    'actionLabel' => 'إضافة تخصص',
    'actionIcon' => 'bi-plus',
    'actionClass' => 'btn-primary',
])

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>الأيقونة</th><th>الاسم</th><th>الحالة</th><th>إجراءات</th></tr></thead>
            <tbody>
                @forelse($specialties as $specialty)
                <tr>
                    <td>
                        @if($specialty->icon)
                            <img src="{{ \App\Helpers\FileUploader::url($specialty->icon) }}" alt="" class="img-thumb">
                        @else <span class="text-muted">—</span> @endif
                    </td>
                    <td class="text-break">{{ $specialty->name }}</td>
                    <td><span class="badge {{ $specialty->is_active ? 'badge-active' : 'badge-inactive' }}">{{ $specialty->is_active ? 'مفعّل' : 'موقوف' }}</span></td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.specialties.edit', $specialty) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.specialties.toggle-status', $specialty) }}" method="POST">@csrf @method('PATCH')<button class="btn btn-outline-warning btn-sm"><i class="bi bi-toggle-on"></i></button></form>
                            <form action="{{ route('admin.specialties.destroy', $specialty) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">لا توجد تخصصات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($specialties->hasPages())<div class="card-footer">{{ $specialties->links() }}</div>@endif
</div>
@endsection
