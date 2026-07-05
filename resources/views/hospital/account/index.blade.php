@extends('layouts.hospital')

@section('page-title', 'المحفظة والاشتراك')

@section('content')
<div class="row g-3 g-md-4 mb-3 mb-md-4">
    <div class="col-6 col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body text-center py-3">
                <div class="icon bg-success bg-opacity-10 text-success mx-auto mb-2"><i class="bi bi-wallet2"></i></div>
                <h4 class="mb-0">{{ number_format($wallet->balance, 2) }} د.ل</h4>
                <small class="text-muted">رصيد المحفظة</small>
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

<div class="row g-3 g-md-4 mb-3 mb-md-4">
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-credit-card"></i> حالة الاشتراك</h6></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">نوع الاشتراك</dt>
                    <dd class="col-sm-7">{{ $hospital->subscriptionTypeLabel() }}</dd>

                    <dt class="col-sm-5">الحالة</dt>
                    <dd class="col-sm-7">
                        @if($hospital->subscription_status)
                            <span class="badge {{ $hospital->subscription_status->badgeClass() }}">{{ $hospital->subscription_status->label() }}</span>
                        @else
                            —
                        @endif
                    </dd>

                    @if($hospital->subscription_type?->value === 'monthly')
                        <dt class="col-sm-5">السعر الشهري</dt>
                        <dd class="col-sm-7">{{ number_format($hospital->monthly_price ?? 0, 2) }} د.ل</dd>
                    @endif

                    @if($hospital->subscription_type?->value === 'usage_based')
                        <dt class="col-sm-5">رسوم كل حجز</dt>
                        <dd class="col-sm-7">{{ number_format($hospital->usage_fee_per_booking ?? 0, 2) }} د.ل</dd>
                    @endif

                    @if($hospital->trial_ends_at)
                        <dt class="col-sm-5">نهاية الفترة المجانية</dt>
                        <dd class="col-sm-7">{{ $hospital->trial_ends_at->format('Y-m-d') }}</dd>
                    @endif

                    @if($hospital->subscription_starts_at)
                        <dt class="col-sm-5">بداية الاشتراك</dt>
                        <dd class="col-sm-7">{{ $hospital->subscription_starts_at->format('Y-m-d') }}</dd>
                    @endif

                    @if($hospital->subscription_ends_at)
                        <dt class="col-sm-5">نهاية الاشتراك</dt>
                        <dd class="col-sm-7">{{ $hospital->subscription_ends_at->format('Y-m-d') }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card h-100 border-info">
            <div class="card-body">
                <h6 class="text-info"><i class="bi bi-info-circle"></i> للعلم</h6>
                <p class="text-muted small mb-2">هذه الصفحة للعرض فقط. لشحن المحفظة أو تعديل الاشتراك، تواصل مع إدارة موعدي.</p>
                @if($hospital->subscription_type?->value === 'usage_based')
                    <p class="text-muted small mb-0">عند تأكيد أي حجز، يُخصم {{ number_format($hospital->usage_fee_per_booking ?? 0, 2) }} د.ل من رصيد المحفظة تلقائياً.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white"><h6 class="mb-0">آخر المعاملات</h6></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>النوع</th>
                    <th>المبلغ</th>
                    <th class="d-none d-md-table-cell">الرصيد بعد</th>
                    <th class="d-none d-lg-table-cell">السبب</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                    <td><span class="badge bg-secondary">{{ $tx->type->label() }}</span></td>
                    <td>{{ number_format($tx->amount, 2) }} د.ل</td>
                    <td class="d-none d-md-table-cell">{{ number_format($tx->balance_after, 2) }} د.ل</td>
                    <td class="d-none d-lg-table-cell text-break">{{ $tx->reason }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">لا توجد معاملات بعد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())<div class="card-footer">{{ $transactions->links() }}</div>@endif
</div>
@endsection
