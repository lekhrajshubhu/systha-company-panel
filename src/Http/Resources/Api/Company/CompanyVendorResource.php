<?php

namespace Systha\CompanyPanel\Http\Resources\Api\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyVendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \Systha\Core\Models\VendorModel $this */
        return [
            'id'          => $this->id,
            'vendor_code' => $this->vendor_code,
            'type'        => $this->type,
            'name'        => $this->name,
            'phone'       => $this->phone,
            'email'       => $this->email,
            'city'        => $this->address?->city,
            'status'      => $this->status ?? 'active',
        ];
    }
}
