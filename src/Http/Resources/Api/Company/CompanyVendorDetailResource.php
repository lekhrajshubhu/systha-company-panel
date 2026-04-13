<?php

namespace Systha\CompanyPanel\Http\Resources\Api\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyVendorDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \Systha\Core\Models\VendorModel $this */
        $primaryContact = $this->primaryContact;
        $primaryClient = $primaryContact?->client;

        return [
            'id' => $this->id,
            'vendor_code' => $this->vendor_code,
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status ?? 'active',
            'phone' => $this->phone,
            'email' => $this->email,

            'address' => [
                'id' => $this->address?->id,
                'add1' => $this->address?->add1 ?? '',
                'add2' => $this->address?->add2 ?? '',
                'city' => $this->address?->city ?? '',
                'state' => $this->address?->state ?? '',
                'zip' => $this->address?->zip ?? '',
            ],

            'contact' => [
                'id' => $primaryContact?->id,
                'first_name' => $primaryClient?->fname ?? '',
                'last_name' => $primaryClient?->lname ?? '',
                'phone' => $primaryClient?->phone ?? '',
                'email' => $primaryContact?->email ?? '',
            ],

            'application_date' => optional($this->created_at)->toDateTimeString(),
            'approved_at' => data_get($this, 'approved_at'),
            'approved_by_name' => (string) data_get($this, 'approved_by_name', ''),
            'approved_ip' => (string) data_get($this, 'approved_ip', ''),
            'notes' => (string) data_get($this, 'notes', ''),

            'attachments' => data_get($this, 'attachments', []),
            'audit' => data_get($this, 'audit', []),
        ];
    }
}
