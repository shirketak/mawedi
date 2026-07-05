<?php

namespace App\Http\Requests\Hospital;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_slot_id' => [
                'required',
                'integer',
                Rule::exists('doctor_slots', 'id')
                    ->where('status', 'available'),
            ],
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_phone' => ['required', 'string', 'max:20'],
        ];
    }

    public function attributes(): array
    {
        return [
            'doctor_slot_id' => 'الموعد',
            'patient_name' => 'اسم المريض',
            'patient_phone' => 'هاتف المريض',
        ];
    }
}
