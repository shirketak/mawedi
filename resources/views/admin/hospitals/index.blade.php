@extends('layouts.admin')

@section('page-title', 'إدارة المستشفيات')

@section('content')
<div class="card mb-3 mb-md-4">
    <div class="card-body">
        <form method="GET" class="row g-3 filter-form">
            <div class="col-12 col-sm-6 col-lg-4">
                <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <select name="governorate" class="form-select">
                    <option value="">كل المحافظات</option>
                    @foreach($governorates as $value => $label)
                        <option value="{{ $value }}" @selected(request('governorate') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <select name="is_active" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="1" @selected(request('is_active') === '1')>مفعّل</option>
                    <option value="0" @selected(request('is_active') === '0')>موقوف</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> بحث</button>
                    <a href="{{ route('admin.hospitals.index') }}" class="btn btn-outline-secondary">إعادة</a>
                </div>
            </div>
        </form>
    </div>
</div>

@include('partials.page-toolbar', [
    'title' => 'قائمة المستشفيات (' . $hospitals->total() . ')',
    'actionUrl' => route('admin.hospitals.create'),
    'actionLabel' => 'إضافة مستشفى',
    'actionIcon' => 'bi-plus',
    'actionClass' => 'btn-primary',
])

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 table-scroll">
            <thead>
                <tr>
                    <th>الشعار</th>
                    <th>الاسم</th>
                    <th class="d-none d-lg-table-cell">المحافظة</th>
                    <th class="d-none d-md-table-cell">الهاتف</th>
                    <th class="d-none d-xl-table-cell">البريد</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hospitals as $hospital)
                <tr>
                    <td>
                        @if($hospital->logo)
                            <img src="{{ \App\Helpers\FileUploader::url($hospital->logo) }}" alt="" class="img-thumb">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-break">{{ $hospital->name }}</td>
                    <td class="d-none d-lg-table-cell">{{ $hospital->governorateLabel() }}</td>
                    <td class="d-none d-md-table-cell">{{ $hospital->phone }}</td>
                    <td class="d-none d-xl-table-cell text-break">{{ $hospital->email }}</td>
                    <td>
                        <span class="badge {{ $hospital->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $hospital->is_active ? 'مفعّل' : 'موقوف' }}
                        </span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.hospitals.edit', $hospital) }}" class="btn btn-outline-primary btn-sm" title="تعديل"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.hospitals.toggle-status', $hospital) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-outline-warning btn-sm" title="تبديل الحالة"><i class="bi bi-toggle-on"></i></button>
                            </form>
                            <form action="{{ route('admin.hospitals.destroy', $hospital) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" title="حذف"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">لا توجد مستشفيات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($hospitals->hasPages())
        <div class="card-footer">{{ $hospitals->links() }}</div>
    @endif
</div>
@endsection
