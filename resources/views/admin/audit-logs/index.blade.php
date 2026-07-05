@extends('layouts.admin')

@section('page-title', 'سجل التدقيق')

@section('content')
<div class="card mb-3 mb-md-4">
    <div class="card-body">
        <form method="GET" class="row g-3 filter-form">
            <div class="col-12 col-sm-6 col-lg-3">
                <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <select name="action" class="form-select">
                    <option value="">كل الإجراءات</option>
                    @foreach($actions as $action)
                        <option value="{{ $action->value }}" @selected(request('action') === $action->value)>{{ $action->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="من تاريخ">
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="إلى تاريخ">
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> بحث</button>
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary">إعادة</a>
                </div>
            </div>
        </form>
    </div>
</div>

@include('partials.page-toolbar', [
    'title' => 'سجل التدقيق (' . $logs->total() . ')',
    'actionUrl' => null,
])

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 table-scroll">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>الإجراء</th>
                    <th class="d-none d-md-table-cell">المستخدم</th>
                    <th class="d-none d-lg-table-cell">الكيان</th>
                    <th class="d-none d-xl-table-cell">IP</th>
                    <th class="d-none d-lg-table-cell">التفاصيل</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="small">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                    <td><span class="badge bg-primary">{{ $log->action->label() }}</span></td>
                    <td class="d-none d-md-table-cell">{{ $log->user?->name ?? '—' }}</td>
                    <td class="d-none d-lg-table-cell small">
                        @if($log->auditable)
                            {{ class_basename($log->auditable_type) }}
                            @if(method_exists($log->auditable, 'name'))
                                : {{ $log->auditable->name }}
                            @endif
                        @else
                            —
                        @endif
                    </td>
                    <td class="d-none d-xl-table-cell small" dir="ltr">{{ $log->ip_address ?? '—' }}</td>
                    <td class="d-none d-lg-table-cell small text-muted">
                        @if($log->old_values || $log->new_values)
                            <span title="{{ json_encode(['old' => $log->old_values, 'new' => $log->new_values], JSON_UNESCAPED_UNICODE) }}">
                                {{ Str::limit(json_encode($log->new_values ?? $log->old_values, JSON_UNESCAPED_UNICODE), 60) }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">لا توجد سجلات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="card-footer">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
