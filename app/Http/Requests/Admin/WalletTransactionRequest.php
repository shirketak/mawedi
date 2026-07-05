<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WalletTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['deposit', 'deduct', 'adjust'])],
            'amount' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'reason' => ['required', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'action' => 'نوع العملية',
            'amount' => 'المبلغ',
            'reason' => 'السبب',
        ];
    }
}
