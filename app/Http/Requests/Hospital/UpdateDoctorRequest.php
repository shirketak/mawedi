<?php

namespace App\Http\Requests\Hospital;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hospital = $this->user()->hospital;

        return [
            'name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'specialty_id' => [
                'required',
                'integer',
                Rule::exists('hospital_specialty', 'specialty_id')
                    ->where('hospital_id', $hospital->id),
            ],
            'consultation_duration_minutes' => ['required', 'integer', 'min:5', 'max:180'],
        ];
    }

    public function attributes(): array
    {
        return (new StoreDoctorRequest)->attributes();
    }
}
