@extends('layouts.admin')

@section('page-title', 'لوحة التحكم')

@section('content')
<div class="row g-3 g-md-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-building"></i></div>
                <div class="min-w-0">
                    <div class="text-muted small">المستشفيات</div>
                    <h4 class="mb-0">{{ $stats['hospitals'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <div class="icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                <div class="min-w-0">
                    <div class="text-muted small">مفعّلة</div>
                    <h4 class="mb-0">{{ $stats['active_hospitals'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-heart-pulse"></i></div>
                <div class="min-w-0">
                    <div class="text-muted small">التخصصات</div>
                    <h4 class="mb-0">{{ $stats['specialties'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <div class="icon bg-info bg-opacity-10 text-info"><i class="bi bi-activity"></i></div>
                <div class="min-w-0">
                    <div class="text-muted small">تخصصات مفعّلة</div>
                    <h4 class="mb-0">{{ $stats['active_specialties'] }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
        <h6 class="mb-0">أحدث المستشفيات</h6>
        <a href="{{ route('admin.hospitals.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> إضافة مستشفى</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 table-scroll">
            <thead><tr><th>الاسم</th><th>المحافظة</th><th class="d-none d-md-table-cell">الهاتف</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @forelse($recentHospitals as $hospital)
                <tr>
                    <td class="text-break">{{ $hospital->name }}</td>
                    <td>{{ $hospital->governorateLabel() }}</td>
                    <td class="d-none d-md-table-cell">{{ $hospital->phone }}</td>
                    <td>
                        <span class="badge {{ $hospital->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $hospital->is_active ? 'مفعّل' : 'موقوف' }}
                        </span>
                    </td>
                    <td><a href="{{ route('admin.hospitals.edit', $hospital) }}" class="btn btn-sm btn-outline-primary">تعديل</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">لا توجد مستشفيات بعد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
