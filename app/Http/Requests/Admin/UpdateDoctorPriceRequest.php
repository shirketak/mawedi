<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'consultation_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }

    public function attributes(): array
    {
        return [
            'consultation_price' => 'سعر الكشف',
        ];
    }
}
