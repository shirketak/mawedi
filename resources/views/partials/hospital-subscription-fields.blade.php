@php
    $selectedType = old('subscription_type', isset($hospital) ? $hospital->subscription_type?->value : 'monthly');
    $monthlyPrice = old('monthly_price', isset($hospital) ? $hospital->monthly_price : ($defaultMonthlyPrice ?? 0));
    $usageFee = old('usage_fee_per_booking', isset($hospital) ? $hospital->usage_fee_per_booking : ($defaultUsageFee ?? 0));
@endphp

<div class="col-12"><hr class="my-2"><h6>إعدادات الاشتراك</h6></div>
<div class="col-12 col-md-6">
    <label class="form-label">نوع الاشتراك *</label>
    <select name="subscription_type" id="subscriptionType" class="form-select" required>
        @foreach($subscriptionTypes as $value => $label)
            <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="col-12 col-md-6" id="monthlyPriceGroup">
    <label class="form-label">سعر الاشتراك الشهري (د.ل) *</label>
    <input type="number" name="monthly_price" id="monthlyPrice" class="form-control" step="0.01" min="0"
        value="{{ $monthlyPrice }}">
    <small class="text-muted">يُطبَّق على هذا المستشفى فقط</small>
</div>
<div class="col-12 col-md-6" id="usageFeeGroup">
    <label class="form-label">رسوم كل حجز (د.ل) *</label>
    <input type="number" name="usage_fee_per_booking" id="usageFee" class="form-control" step="0.01" min="0"
        value="{{ $usageFee }}">
    <small class="text-muted">تُخصم من المحفظة عند تأكيد الحجز</small>
</div>

@push('scripts')
<script>
(function () {
    const typeSelect = document.getElementById('subscriptionType');
    const monthlyGroup = document.getElementById('monthlyPriceGroup');
    const usageGroup = document.getElementById('usageFeeGroup');
    const monthlyInput = document.getElementById('monthlyPrice');
    const usageInput = document.getElementById('usageFee');

    function toggleSubscriptionFields() {
        const type = typeSelect.value;
        const isMonthly = type === 'monthly';
        const isUsage = type === 'usage_based';

        monthlyGroup.style.display = isMonthly ? '' : 'none';
        usageGroup.style.display = isUsage ? '' : 'none';
        monthlyInput.required = isMonthly;
        usageInput.required = isUsage;
    }

    typeSelect?.addEventListener('change', toggleSubscriptionFields);
    toggleSubscriptionFields();
})();
</script>
@endpush
