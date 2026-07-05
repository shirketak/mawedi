<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'app_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_twitter' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'privacy_policy' => ['nullable', 'string'],
            'terms_of_use' => ['nullable', 'string'],
            'default_monthly_price' => ['required', 'numeric', 'min:0'],
            'default_usage_fee_per_booking' => ['required', 'numeric', 'min:0'],
            'default_free_trial_days' => ['required', 'integer', 'min:0', 'max:365'],
        ];
    }
}
