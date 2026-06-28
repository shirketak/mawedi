<?php

namespace App\Http\Requests\Hospital;

use App\Enums\VacationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDoctorVacationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date', 'after_or_equal:today'],
            'type' => ['required', Rule::enum(VacationType::class)],
            'reason' => ['nullable', 'string', 'max:500'],
            'reschedule_bookings' => ['nullable', 'boolean'],
            'reschedule_reason' => ['required_if:reschedule_bookings,1', 'nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'date' => 'تاريخ الإجازة',
            'type' => 'نوع الإجازة',
            'reason' => 'السبب',
            'reschedule_bookings' => 'تأجيل الحجوزات',
            'reschedule_reason' => 'سبب التأجيل',
        ];
    }
}
