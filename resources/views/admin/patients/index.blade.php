@extends('layouts.admin')

@section('page-title', 'إدارة المستخدمين')

@section('content')
<div class="card mb-3 mb-md-4">
    <div class="card-body">
        <form method="GET" class="row g-3 filter-form">
            <div class="col-12 col-sm-6 col-lg-4">
                <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو الهاتف أو البريد..." value="{{ request('search') }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <select name="is_active" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="1" @selected(request('is_active') === '1')>مفعّل</option>
                    <option value="0" @selected(request('is_active') === '0')>موقوف</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <select name="sort" class="form-select">
                    <option value="created_at" @selected(request('sort', 'created_at') === 'created_at')>تاريخ التسجيل</option>
                    <option value="name" @selected(request('sort') === 'name')>الاسم</option>
                    <option value="last_login_at" @selected(request('sort') === 'last_login_at')>آخر دخول</option>
                    <option value="bookings_count" @selected(request('sort') === 'bookings_count')>عدد الحجوزات</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <select name="direction" class="form-select">
                    <option value="desc" @selected(request('direction', 'desc') === 'desc')>تنازلي</option>
                    <option value="asc" @selected(request('direction') === 'asc')>تصاعدي</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> بحث</button>
                    <a href="{{ route('admin.patients.index') }}" class="btn btn-outline-secondary">إعادة</a>
                </div>
            </div>
        </form>
    </div>
</div>

@include('partials.page-toolbar', [
    'title' => 'قائمة المستخدمين (' . $patients->total() . ')',
    'actionUrl' => null,
])

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 table-scroll">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th class="d-none d-md-table-cell">الهاتف</th>
                    <th class="d-none d-lg-table-cell">البريد</th>
                    <th class="d-none d-md-table-cell">الحجوزات</th>
                    <th>الحالة</th>
                    <th class="d-none d-xl-table-cell">آخر دخول</th>
                    <th class="d-none d-lg-table-cell">تاريخ التسجيل</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                <tr>
                    <td class="text-break">
                        <a href="{{ route('admin.patients.show', $patient) }}" class="text-decoration-none">{{ $patient->name }}</a>
                    </td>
                    <td class="d-none d-md-table-cell" dir="ltr">{{ $patient->phone }}</td>
                    <td class="d-none d-lg-table-cell text-break">{{ $patient->email ?? '—' }}</td>
                    <td class="d-none d-md-table-cell">{{ $patient->bookings_count }}</td>
                    <td>
                        <span class="badge {{ $patient->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $patient->is_active ? 'مفعّل' : 'موقوف' }}
                        </span>
                    </td>
                    <td class="d-none d-xl-table-cell small">{{ $patient->last_login_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td class="d-none d-lg-table-cell small">{{ $patient->created_at?->format('Y-m-d') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-outline-info btn-sm" title="عرض"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.patients.edit', $patient) }}" class="btn btn-outline-primary btn-sm" title="تعديل"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.patients.toggle-status', $patient) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-outline-warning btn-sm" title="تبديل الحالة"><i class="bi bi-toggle-on"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">لا يوجد مستخدمون</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($patients->hasPages())
        <div class="card-footer">{{ $patients->links() }}</div>
    @endif
</div>
@endsection
