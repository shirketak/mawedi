<?php

namespace App\Http\Requests\Hospital;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHospitalProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hospital = $this->user()->hospital;

        return [
            'phone' => ['required', 'string', 'max:20'],
            'phone_secondary' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:hospitals,email,'.$hospital->id],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'map_url' => ['nullable', 'url', 'max:500'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'phone' => 'رقم الهاتف',
            'phone_secondary' => 'رقم هاتف إضافي',
            'email' => 'البريد الإلكتروني',
            'website' => 'الموقع الإلكتروني',
            'address' => 'العنوان',
            'map_url' => 'رابط الموقع على الخريطة',
            'logo' => 'الشعار',
        ];
    }
}
