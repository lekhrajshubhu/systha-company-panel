<?php

namespace Systha\CompanyPanel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CredentialStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100'],
            'mode' => ['required', 'string', Rule::in(['test', 'live'])],
            'credentials' => ['required', 'array'],
            'credentials.publishable_key' => ['required', 'string', 'max:255'],
            'credentials.secret_key' => ['required', 'string', 'max:255'],
            'credentials.webhook_secret' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
