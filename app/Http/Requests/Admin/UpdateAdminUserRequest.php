<?php

namespace App\Http\Requests\Admin;

use App\Enums\AdminRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->user()?->hasPermission('admin_users') ?? false;
    }

    public function rules(): array
    {
        $admin = $this->route('admin_user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($admin?->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::enum(AdminRole::class)],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return (new StoreAdminUserRequest)->attributes();
    }
}
