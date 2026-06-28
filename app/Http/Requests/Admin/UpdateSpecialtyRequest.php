<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSpecialtyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $specialty = $this->route('specialty');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('specialties', 'name')->ignore($specialty->id)],
            'icon' => ['nullable', 'image', 'max:1024'],
        ];
    }

    public function attributes(): array
    {
        return (new StoreSpecialtyRequest)->attributes();
    }
}
