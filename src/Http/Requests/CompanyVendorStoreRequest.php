<?php

namespace Systha\CompanyPanel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyVendorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // adjust authorization logic if needed
        return true;
    }

    public function rules(): array
    {
        return [
            // basic
            'basic.name'  => ['required', 'string', 'max:255'],
            'basic.type'  => ['nullable', 'string', 'max:120'],
            'basic.email' => ['nullable', 'email', 'max:255', Rule::unique('vendors', 'email')],
            'basic.phone' => ['nullable', 'string', 'max:30'],

            // address (matches VendorFormPage fields)
            'address.line1' => ['required', 'string', 'max:255'],
            'address.line2' => ['nullable', 'string', 'max:255'],
            'address.city'   => ['required', 'string', 'max:120'],
            'address.state'  => ['required', 'string', 'max:120'],
            'address.zip'    => ['required', 'string', 'max:20'],
            'address.lat'    => ['nullable', 'numeric'],
            'address.lng'    => ['nullable', 'numeric'],

            // contact person
            'contact.first_name' => ['required', 'string', 'max:100'],
            'contact.last_name'  => ['required', 'string', 'max:100'],
            'contact.email'      => ['required', 'email', 'max:255'],
            'contact.email2'     => ['nullable', 'email', 'max:255'],
            'contact.phone1'     => ['required', 'string', 'max:30'],
            'contact.phone2'     => ['nullable', 'string', 'max:30'],
        ];
    }
}