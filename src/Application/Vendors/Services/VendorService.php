<?php

namespace Systha\CompanyPanel\Application\Vendors\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Systha\Core\Models\Vendor;
use Systha\Core\Models\Company;
use Systha\Core\Models\VendorUser;
use Systha\CompanyPanel\Application\Vendors\Dto\CompanyVendorDto;
use Systha\CompanyPanel\Application\Vendors\Dto\ContactDto;
use Systha\CompanyPanel\Application\Users\Dto\ClientDto;
use Systha\CompanyPanel\Application\Users\Services\AddressService;
use Systha\CompanyPanel\Application\Users\Services\ClientService;
use Systha\Core\Models\VendorModel;

class VendorService
{
    public function __construct(
        private readonly ClientService $clientService,
        private readonly AddressService $addressService,
    ) {
    }

    /**
     * Create vendor and associated contact/address inside DB transaction.
     */
    public function createForCompany(Company $company, CompanyVendorDto $dto): VendorModel
    {
        return DB::transaction(function () use ($company, $dto) {
            $vendorDto = $dto->vendor;
            $contactDto = $dto->contact;
            $addressDto = $dto->address;

            // Map ContactDto to ClientDto for the ClientService
            $clientDto = new ClientDto(
                fname: $contactDto->first_name,
                lname: $contactDto->last_name,
                email: $contactDto->email,
                email2: $contactDto->email2,
                phone1: $contactDto->phone1,
                phone2: $contactDto->phone2
            );

            // optionally create a client/contact record via ClientService (if you keep clients centralized)
            $clientResult = $this->clientService->createOrUpdate($clientDto);
            $client = $clientResult['client'] ?? null;

            $vendor = VendorModel::query()->create([
                'company_id' => $company->id,
                'name'       => $vendorDto->name,
                'type'       => $vendorDto->type,
                'email'      => $vendorDto->email,
                'phone'      => $vendorDto->phone,
                'client_id'  => $client?->id ?? null,
            ]);

            // attach default address via AddressService if present
            $this->addressService->createOrUpdateDefault($vendor, $addressDto);

            // Create primary VendorUser record
            if ($client) {
                VendorUser::query()->create([
                    'vendor_id'  => $vendor->id,
                    'client_id'  => $client->id,
                    'email'      => $client->email,
                    'password'   => Hash::make(Str::password(10)),
                    'is_primary' => true,
                    'status'     => 'pending',
                ]);
            }

            return $vendor->refresh();
        });
    }
}