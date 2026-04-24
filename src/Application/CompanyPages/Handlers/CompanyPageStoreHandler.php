<?php

namespace Systha\CompanyPanel\Application\CompanyPages\Handlers;

use Systha\CompanyPanel\Application\CompanyPages\Dto\CompanyPageDto;
use Systha\Core\Models\Company;
use Systha\Core\Models\CompanyPage;

class CompanyPageStoreHandler
{
    public function handle(Company $company, array $payload): CompanyPage
    {
        $dto = CompanyPageDto::fromArray($payload);

        return $company->companyPages()->create($dto->toArray());
    }

    public function update(CompanyPage $page, array $payload): CompanyPage
    {
        $dto = CompanyPageDto::fromArray($payload);

        $page->fill($dto->toArray())->save();

        return $page->fresh() ?? $page;
    }
}
