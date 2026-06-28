<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpecialtyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:specialties,name'],
            'icon' => ['nullable', 'image', 'max:1024'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'اسم التخصص',
            'icon' => 'أيقونة التخصص',
        ];
    }
}
