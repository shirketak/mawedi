<?php

namespace App\Http\Requests\Hospital;

use App\Enums\DayOfWeek;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncDoctorScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'schedule' => ['required', 'array'],
            'schedule.*.day_of_week' => ['required', 'integer', Rule::enum(DayOfWeek::class)],
            'schedule.*.periods' => ['nullable', 'array'],
            'schedule.*.periods.*.start_time' => ['required_with:schedule.*.periods', 'date_format:H:i'],
            'schedule.*.periods.*.end_time' => ['required_with:schedule.*.periods', 'date_format:H:i', 'after:schedule.*.periods.*.start_time'],
        ];
    }

    public function attributes(): array
    {
        return [
            'schedule' => 'جدول العمل',
            'schedule.*.day_of_week' => 'يوم العمل',
            'schedule.*.periods.*.start_time' => 'وقت البداية',
            'schedule.*.periods.*.end_time' => 'وقت النهاية',
        ];
    }
}
