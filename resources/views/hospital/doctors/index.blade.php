@extends('layouts.hospital')

@section('page-title', 'إدارة الأطباء')

@section('content')
<div class="card mb-3 mb-md-4">
    <div class="card-body">
        <form method="GET" class="row g-3 filter-form">
            <div class="col-12 col-sm-6 col-lg-4">
                <input type="text" name="search" class="form-control" placeholder="بحث بالاسم..." value="{{ request('search') }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <select name="specialty_id" class="form-select">
                    <option value="">كل التخصصات</option>
                    @foreach($specialties as $s)
                        <option value="{{ $s->id }}" @selected(request('specialty_id') == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">بحث</button>
                    <a href="{{ route('hospital.doctors.index') }}" class="btn btn-outline-secondary">إعادة</a>
                </div>
            </div>
        </form>
    </div>
</div>

@include('partials.page-toolbar', [
    'title' => 'الأطباء (' . $doctors->total() . ')',
    'actionUrl' => route('hospital.doctors.create'),
    'actionLabel' => 'إضافة طبيب',
    'actionIcon' => 'bi-plus',
    'actionClass' => 'btn-success',
])

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 table-scroll">
            <thead>
                <tr>
                    <th>الصورة</th><th>الاسم</th><th class="d-none d-md-table-cell">التخصص</th>
                    <th class="d-none d-lg-table-cell">مدة الكشف</th><th>الحالة</th><th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($doctors as $doctor)
                <tr>
                    <td>
                        @if($doctor->photo)
                            <img src="{{ \App\Helpers\FileUploader::url($doctor->photo) }}" alt="" class="img-thumb img-thumb--circle">
                        @else <i class="bi bi-person-circle fs-4 text-muted"></i> @endif
                    </td>
                    <td class="text-break">{{ $doctor->name }}</td>
                    <td class="d-none d-md-table-cell">{{ $doctor->specialty->name }}</td>
                    <td class="d-none d-lg-table-cell">{{ $doctor->consultation_duration_minutes }} دقيقة</td>
                    <td><span class="badge {{ $doctor->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $doctor->is_active ? 'مفعّل' : 'موقوف' }}</span></td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('hospital.doctors.schedule', $doctor) }}" class="btn btn-outline-primary btn-sm" title="جدول العمل"><i class="bi bi-calendar-week"></i></a>
                            <a href="{{ route('hospital.doctors.vacations', $doctor) }}" class="btn btn-outline-warning btn-sm" title="الإجازات"><i class="bi bi-calendar-x"></i></a>
                            <a href="{{ route('hospital.doctors.edit', $doctor) }}" class="btn btn-outline-secondary btn-sm" title="تعديل"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('hospital.doctors.toggle-status', $doctor) }}" method="POST">@csrf @method('PATCH')<button class="btn btn-outline-info btn-sm" title="تبديل الحالة"><i class="bi bi-toggle-on"></i></button></form>
                            <form action="{{ route('hospital.doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm" title="حذف"><i class="bi bi-trash"></i></button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">لا يوجد أطباء</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($doctors->hasPages())<div class="card-footer">{{ $doctors->links() }}</div>@endif
</div>
@endsection
