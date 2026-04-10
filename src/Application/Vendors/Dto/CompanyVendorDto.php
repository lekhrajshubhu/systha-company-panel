<?php

namespace Systha\CompanyPanel\Application\Vendors\Dto;

use Systha\CompanyPanel\Application\Users\Dto\AddressDto;
use Systha\CompanyPanel\Application\Users\Dto\ClientDto;

final class CompanyVendorDto
{
    public function __construct(
        public VendorDto $vendor,
        public ContactDto $contact,
        public AddressDto $address,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            vendor: VendorDto::fromArray($data['basic'] ?? []),
            contact: ContactDto::fromArray($data['contact'] ?? []),
            address: AddressDto::fromArray($data['address'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'basic' => $this->vendor->toArray(),
            'contact' => $this->contact->toArray(),
            'address' => $this->address->toArray(),
        ];
    }
}
