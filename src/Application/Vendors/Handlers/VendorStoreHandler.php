<?php


namespace Systha\CompanyPanel\Application\Vendors\Handlers;

use Systha\Core\Models\Company;
use Systha\CompanyPanel\Application\Vendors\Dto\CompanyVendorDto;
use Systha\CompanyPanel\Application\Vendors\Services\VendorService;
use Systha\Core\Models\VendorModel;

class VendorStoreHandler
{
    public function __construct(private readonly VendorService $vendorService)
    {
    }

    public function handle(Company $company, CompanyVendorDto $dto): VendorModel
    {
        // additional business checks can be done here
        return $this->vendorService->createForCompany($company, $dto);
    }
}