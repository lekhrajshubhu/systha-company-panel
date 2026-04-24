<?php

namespace Systha\CompanyPanel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyLogoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logo' => ['nullable', 'image', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
        ];
    }
}
