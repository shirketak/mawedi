@extends('layouts.admin')

@section('page-title', 'إدارة المستشفيات')

@section('content')
<div class="card mb-3 mb-md-4">
    <div class="card-body">
        <form method="GET" class="row g-3 filter-form">
            <div class="col-12 col-sm-6 col-lg-3">
                <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
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
            <div class="col-12 col-sm-6 col-lg-2">
                <select name="subscription_type" class="form-select">
                    <option value="">كل أنواع الاشتراك</option>
                    @foreach($subscriptionTypes as $value => $label)
                        <option value="{{ $value }}" @selected(request('subscription_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <select name="subscription_status" class="form-select">
                    <option value="">كل حالات الاشتراك</option>
                    @foreach($subscriptionStatuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('subscription_status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <select name="sort" class="form-select">
                    <option value="created_at" @selected(request('sort', 'created_at') === 'created_at')>تاريخ الإنشاء</option>
                    <option value="name" @selected(request('sort') === 'name')>الاسم</option>
                    <option value="doctors_count" @selected(request('sort') === 'doctors_count')>عدد الأطباء</option>
                    <option value="specialties_count" @selected(request('sort') === 'specialties_count')>عدد التخصصات</option>
                    <option value="bookings_count" @selected(request('sort') === 'bookings_count')>عدد الحجوزات</option>
                    <option value="subscription_ends_at" @selected(request('sort') === 'subscription_ends_at')>انتهاء الاشتراك</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-1">
                <select name="direction" class="form-select">
                    <option value="desc" @selected(request('direction', 'desc') === 'desc')>تنازلي</option>
                    <option value="asc" @selected(request('direction') === 'asc')>تصاعدي</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
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
                    <th class="d-none d-md-table-cell">الأطباء</th>
                    <th class="d-none d-md-table-cell">التخصصات</th>
                    <th class="d-none d-lg-table-cell">الحجوزات</th>
                    <th class="d-none d-xl-table-cell">الاشتراك</th>
                    <th>الحالة</th>
                    <th class="d-none d-xl-table-cell">تواريخ الاشتراك</th>
                    <th class="d-none d-lg-table-cell">المحفظة</th>
                    <th class="d-none d-md-table-cell">تاريخ الإنشاء</th>
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
                    <td class="text-break">
                        <a href="{{ route('admin.hospitals.show', $hospital) }}" class="text-decoration-none">{{ $hospital->name }}</a>
                    </td>
                    <td class="d-none d-lg-table-cell">{{ $hospital->governorateLabel() }}</td>
                    <td class="d-none d-md-table-cell">{{ $hospital->doctors_count }}</td>
                    <td class="d-none d-md-table-cell">{{ $hospital->specialties_count }}</td>
                    <td class="d-none d-lg-table-cell">{{ $hospital->bookings_count }}</td>
                    <td class="d-none d-xl-table-cell">
                        @if($hospital->subscription_status)
                            <span class="badge {{ $hospital->subscription_status->badgeClass() }}">
                                {{ $hospital->subscriptionStatusLabel() }}
                            </span>
                            <small class="d-block text-muted">{{ $hospital->subscriptionTypeLabel() }}</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $hospital->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $hospital->is_active ? 'مفعّل' : 'موقوف' }}
                        </span>
                    </td>
                    <td class="d-none d-xl-table-cell small">
                        @if($hospital->subscription_starts_at)
                            <div>من: {{ $hospital->subscription_starts_at->format('Y-m-d') }}</div>
                        @endif
                        @if($hospital->subscription_ends_at)
                            <div>إلى: {{ $hospital->subscription_ends_at->format('Y-m-d') }}</div>
                        @elseif($hospital->trial_ends_at)
                            <div>تجربة حتى: {{ $hospital->trial_ends_at->format('Y-m-d') }}</div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="d-none d-lg-table-cell">{{ number_format($hospital->walletBalance(), 2) }} د.ل</td>
                    <td class="d-none d-md-table-cell small">{{ $hospital->created_at?->format('Y-m-d') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.hospitals.show', $hospital) }}" class="btn btn-outline-info btn-sm" title="الإحصائيات"><i class="bi bi-bar-chart"></i></a>
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
                <tr><td colspan="12" class="text-center text-muted py-4">لا توجد مستشفيات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($hospitals->hasPages())
        <div class="card-footer">{{ $hospitals->links() }}</div>
    @endif
</div>
@endsection
