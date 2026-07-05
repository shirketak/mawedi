@extends('layouts.admin')

@section('page-title', 'مستخدمو الإدارة')

@section('content')
<div class="card mb-3 mb-md-4">
    <div class="card-body">
        <form method="GET" class="row g-3 filter-form">
            <div class="col-12 col-sm-6 col-lg-4">
                <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو البريد..." value="{{ request('search') }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <select name="role" class="form-select">
                    <option value="">كل الأدوار</option>
                    @foreach($roles as $value => $label)
                        <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
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
                    <a href="{{ route('admin.admin-users.index') }}" class="btn btn-outline-secondary">إعادة</a>
                </div>
            </div>
        </form>
    </div>
</div>

@include('partials.page-toolbar', [
    'title' => 'مستخدمو الإدارة (' . $admins->total() . ')',
    'actionUrl' => route('admin.admin-users.create'),
    'actionLabel' => 'إضافة مستخدم',
    'actionIcon' => 'bi-person-plus',
    'actionClass' => 'btn-primary',
])

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 table-scroll">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th class="d-none d-md-table-cell">البريد</th>
                    <th>الدور</th>
                    <th class="d-none d-lg-table-cell">آخر دخول</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $user)
                <tr>
                    <td class="text-break">
                        {{ $user->name }}
                        @if($user->id === auth('admin')->id())
                            <span class="badge bg-secondary">أنت</span>
                        @endif
                    </td>
                    <td class="d-none d-md-table-cell text-break">{{ $user->email }}</td>
                    <td><span class="badge bg-primary">{{ $user->role?->label() }}</span></td>
                    <td class="d-none d-lg-table-cell">
                        {{ $user->last_login_at?->format('Y-m-d H:i') ?? '—' }}
                    </td>
                    <td>
                        <span class="badge {{ $user->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $user->is_active ? 'مفعّل' : 'موقوف' }}
                        </span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.admin-users.edit', $user) }}" class="btn btn-outline-primary btn-sm" title="تعديل">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($user->id !== auth('admin')->id())
                            <form action="{{ route('admin.admin-users.toggle-status', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-outline-warning btn-sm" title="تبديل الحالة"><i class="bi bi-toggle-on"></i></button>
                            </form>
                            <form action="{{ route('admin.admin-users.destroy', $user) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" title="حذف"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">لا يوجد مستخدمون</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($admins->hasPages())
        <div class="card-footer">{{ $admins->links() }}</div>
    @endif
</div>
@endsection
