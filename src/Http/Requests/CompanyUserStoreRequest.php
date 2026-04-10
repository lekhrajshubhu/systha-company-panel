<?php

namespace Systha\CompanyPanel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyUserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // adjust authorization logic if needed
        return true;
    }

    public function rules(): array
    {
        return [
            'contact.first_name' => ['required', 'string', 'max:100'],
            'contact.last_name'  => ['required', 'string', 'max:100'],
            'contact.email'      => ['required', 'email', 'max:255', Rule::unique('company_users', 'email')],
            'contact.phone1'     => ['required', 'string', 'max:30'],

            'address.line1' => ['nullable', 'string', 'max:255'],
            'address.line2' => ['nullable', 'string', 'max:255'],
            'address.city'  => ['nullable', 'string', 'max:120'],
            'address.state' => ['nullable', 'string', 'max:120'],
            'address.zip'   => ['nullable', 'string', 'max:20'],
            'address.lat'   => ['nullable', 'numeric'],
            'address.lng'   => ['nullable', 'numeric'],
        ];
    }
}