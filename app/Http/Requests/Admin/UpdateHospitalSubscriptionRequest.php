<?php

namespace App\Http\Requests\Admin;

use App\Enums\SubscriptionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHospitalSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subscription_type' => ['required', Rule::enum(SubscriptionType::class)],
            'monthly_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'usage_fee_per_booking' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'subscription_starts_at' => ['nullable', 'date'],
            'subscription_duration_months' => ['nullable', 'integer', 'min:1', 'max:36'],
            'subscription_ends_at' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'subscription_type' => 'نوع الاشتراك',
            'monthly_price' => 'سعر الاشتراك الشهري',
            'usage_fee_per_booking' => 'رسوم الحجز',
            'subscription_starts_at' => 'تاريخ بداية الاشتراك',
            'subscription_duration_months' => 'مدة الاشتراك بالأشهر',
            'subscription_ends_at' => 'تاريخ انتهاء الاشتراك',
        ];
    }
}
