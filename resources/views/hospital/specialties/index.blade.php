@extends('layouts.hospital')

@section('page-title', 'تخصصات المستشفى')

@section('content')
@if($availableSpecialties->isNotEmpty())
<div class="card mb-3 mb-md-4">
    <div class="card-header bg-white"><h6 class="mb-0">إضافة تخصص</h6></div>
    <div class="card-body">
        <form method="POST" action="{{ route('hospital.specialties.store') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-8">
                <label class="form-label">اختر تخصصاً من القائمة</label>
                <select name="specialty_id" class="form-select" required>
                    <option value="">— اختر —</option>
                    @foreach($availableSpecialties as $specialty)
                        <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100"><i class="bi bi-plus"></i> إضافة</button>
            </div>
        </form>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header bg-white"><h6 class="mb-0">التخصصات المتاحة ({{ $specialties->count() }})</h6></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>الأيقونة</th><th>التخصص</th><th>إجراءات</th></tr></thead>
            <tbody>
                @forelse($specialties as $specialty)
                <tr>
                    <td>
                        @if($specialty->icon)
                            <img src="{{ \App\Helpers\FileUploader::url($specialty->icon) }}" alt="" class="img-thumb">
                        @else — @endif
                    </td>
                    <td class="text-break">{{ $specialty->name }}</td>
                    <td>
                        <form action="{{ route('hospital.specialties.destroy', $specialty) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger w-100 w-sm-auto"><i class="bi bi-trash"></i> حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-4">لم يتم إضافة تخصصات بعد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
