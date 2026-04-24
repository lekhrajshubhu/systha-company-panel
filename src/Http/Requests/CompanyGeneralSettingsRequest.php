<?php

namespace Systha\CompanyPanel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyGeneralSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'email_alt' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'phone_alt' => ['nullable', 'string', 'max:30'],
            'meta' => ['nullable', 'array'],
            'url' => ['nullable', 'url', 'max:255'],
            'reg_no' => ['nullable', 'string', 'max:120'],

            'address.line_1' => ['nullable', 'string', 'max:255'],
            'address.line_2' => ['nullable', 'string', 'max:255'],
            'address.city' => ['nullable', 'string', 'max:120'],
            'address.state' => ['nullable', 'string', 'max:120'],
            'address.zip' => ['nullable', 'string', 'max:20'],
        ];
    }
}
