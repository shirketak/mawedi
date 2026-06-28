@extends('layouts.hospital')

@section('page-title', 'سجل تأجيل المواعيد')

@section('content')
<div class="card mb-3 mb-md-4">
    <div class="card-body">
        <form method="GET" class="row g-3 filter-form">
            <div class="col-12 col-sm-6 col-lg-4">
                <select name="doctor_id" class="form-select">
                    <option value="">كل الأطباء</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected(($filters['doctor_id'] ?? '') == $doctor->id)>{{ $doctor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}" placeholder="من تاريخ">
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">بحث</button>
                    <a href="{{ route('hospital.reschedule-logs.index') }}" class="btn btn-outline-secondary">إعادة</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white"><h6 class="mb-0">سجل عمليات التأجيل ({{ $logs->total() }})</h6></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 table-scroll">
            <thead><tr><th>التاريخ</th><th>الطبيب</th><th>اليوم الأصلي</th><th class="d-none d-md-table-cell">السبب</th><th>العدد</th><th></th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-nowrap">{{ $log->created_at->format('Y-m-d') }}<br><small class="text-muted d-sm-none">{{ $log->created_at->format('H:i') }}</small></td>
                    <td class="text-break">{{ $log->doctor->name }}</td>
                    <td>{{ $log->original_date->format('Y-m-d') }}</td>
                    <td class="d-none d-md-table-cell text-break">{{ $log->reason }}</td>
                    <td><span class="badge bg-primary">{{ $log->details['total_moved'] ?? 0 }}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#log-{{ $log->id }}" aria-expanded="false">عرض</button>
                    </td>
                </tr>
                <tr class="collapse" id="log-{{ $log->id }}">
                    <td colspan="6" class="bg-light p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>المريض</th><th>من</th><th>إلى</th></tr></thead>
                                <tbody>
                                    @foreach($log->details['moves'] ?? [] as $move)
                                    <tr>
                                        <td class="text-break">{{ $move['patient_name'] }}</td>
                                        <td class="text-nowrap">{{ $move['from']['date'] }} {{ substr($move['from']['time'], 0, 5) }}</td>
                                        <td class="text-nowrap">{{ $move['to']['date'] }} {{ substr($move['to']['time'], 0, 5) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">لا توجد سجلات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())<div class="card-footer">{{ $logs->links() }}</div>@endif
</div>
@endsection
