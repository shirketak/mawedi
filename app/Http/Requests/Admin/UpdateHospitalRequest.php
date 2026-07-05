<?php

namespace App\Http\Requests\Admin;

use App\Enums\LibyaGovernorate;
use App\Enums\SubscriptionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHospitalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hospital = $this->route('hospital');

        return [
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'map_url' => ['nullable', 'url', 'max:500'],
            'governorate' => ['required', Rule::enum(LibyaGovernorate::class)],
            'phone' => ['required', 'string', 'max:20'],
            'phone_secondary' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', Rule::unique('hospitals', 'email')->ignore($hospital->id)],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'subscription_type' => ['required', Rule::enum(SubscriptionType::class)],
            'monthly_price' => [
                Rule::requiredIf(fn () => $this->input('subscription_type') === SubscriptionType::Monthly->value),
                'nullable', 'numeric', 'min:0', 'max:999999.99',
            ],
            'usage_fee_per_booking' => [
                Rule::requiredIf(fn () => $this->input('subscription_type') === SubscriptionType::UsageBased->value),
                'nullable', 'numeric', 'min:0', 'max:9999.99',
            ],
        ];
    }

    public function attributes(): array
    {
        return (new StoreHospitalRequest)->attributes();
    }
}
