<?php

namespace Systha\CompanyPanel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyPageStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $company = $this->get('company');
        $pageId = $this->route('id');

        $slugRule = Rule::unique('company_pages', 'slug')->where(function ($query) use ($company) {
            if ($company) {
                $query->where('company_id', $company->id);
            }

            $query->whereNull('deleted_at');

            return $query;
        });

        if ($pageId) {
            $slugRule = $slugRule->ignore($pageId);
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', $slugRule],
            'content' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['published', 'draft'])],
        ];
    }
}
