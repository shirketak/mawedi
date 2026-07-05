@extends('layouts.admin')

@section('page-title', 'محفظة المستشفى')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 mb-3 mb-md-4">
    <div class="min-w-0">
        <h5 class="mb-1 text-break">{{ $hospital->name }}</h5>
        <div class="text-muted small">إدارة المحفظة والمعاملات</div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.hospitals.show', $hospital) }}" class="btn btn-outline-info btn-sm">
            <i class="bi bi-bar-chart"></i> الإحصائيات
        </a>
        <a href="{{ route('admin.hospitals.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i> العودة
        </a>
    </div>
</div>

<div class="row g-3 g-md-4 mb-4">
    <div class="col-6 col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body text-center py-3">
                <div class="icon bg-success bg-opacity-10 text-success mx-auto mb-2"><i class="bi bi-wallet2"></i></div>
                <h4 class="mb-0">{{ number_format($wallet->balance, 2) }} د.ل</h4>
                <small class="text-muted">الرصيد الحالي</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body text-center py-3">
                <div class="icon bg-primary bg-opacity-10 text-primary mx-auto mb-2"><i class="bi bi-arrow-down-circle"></i></div>
                <h4 class="mb-0">{{ number_format($wallet->total_deposits, 2) }} د.ل</h4>
                <small class="text-muted">إجمالي الإيداعات</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body text-center py-3">
                <div class="icon bg-danger bg-opacity-10 text-danger mx-auto mb-2"><i class="bi bi-arrow-up-circle"></i></div>
                <h4 class="mb-0">{{ number_format($wallet->total_deductions, 2) }} د.ل</h4>
                <small class="text-muted">إجمالي الخصومات</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 g-md-4 mb-4">
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white"><h6 class="mb-0">عملية جديدة</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.hospitals.wallet.store', $hospital) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">نوع العملية *</label>
                        <select name="action" class="form-select" required id="walletAction">
                            <option value="deposit" @selected(old('action') === 'deposit')>إيداع</option>
                            <option value="deduct" @selected(old('action') === 'deduct')>خصم</option>
                            <option value="adjust" @selected(old('action') === 'adjust')>تعديل الرصيد</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" id="amountLabel">المبلغ *</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0"
                            value="{{ old('amount') }}" required>
                        <small class="text-muted d-none" id="adjustHint">أدخل الرصيد الجديد المطلوب</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">السبب *</label>
                        <textarea name="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">تنفيذ العملية</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white"><h6 class="mb-0">سجل المعاملات</h6></div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-scroll">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>النوع</th>
                            <th class="d-none d-md-table-cell">المبلغ</th>
                            <th class="d-none d-lg-table-cell">قبل</th>
                            <th class="d-none d-lg-table-cell">بعد</th>
                            <th class="d-none d-xl-table-cell">السبب</th>
                            <th class="d-none d-md-table-cell">بواسطة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                        <tr>
                            <td class="small">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                            <td><span class="badge {{ $tx->type->badgeClass() }}">{{ $tx->type->label() }}</span></td>
                            <td class="d-none d-md-table-cell">{{ number_format($tx->amount, 2) }} د.ل</td>
                            <td class="d-none d-lg-table-cell">{{ number_format($tx->balance_before, 2) }}</td>
                            <td class="d-none d-lg-table-cell">{{ number_format($tx->balance_after, 2) }}</td>
                            <td class="d-none d-xl-table-cell text-break small">{{ $tx->reason }}</td>
                            <td class="d-none d-md-table-cell small">{{ $tx->performedBy?->name ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">لا توجد معاملات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="card-footer">{{ $transactions->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('walletAction')?.addEventListener('change', function () {
    const isAdjust = this.value === 'adjust';
    document.getElementById('amountLabel').textContent = isAdjust ? 'الرصيد الجديد *' : 'المبلغ *';
    document.getElementById('adjustHint').classList.toggle('d-none', !isAdjust);
});
</script>
@endpush
