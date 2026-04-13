<?php

namespace Systha\CompanyPanel\Application\PaymentCredentials\Handlers;

use Systha\CompanyPanel\Application\PaymentCredentials\Dto\CompanyPaymentCredentialDto;
use Systha\Core\Models\Company;
use Systha\Core\Models\CompanyPaymentCredential;

class CredentialStoreHandler
{
    public function handle(Company $company, array $payload): CompanyPaymentCredential
    {
        $dto = CompanyPaymentCredentialDto::fromArray($payload);

        return $company->paymentCredential()->create([
            'gateway_name' => $dto->name,
            'code' => $dto->code,
            'mode' => $dto->mode,
            'credentials' => $dto->credentialsArray(),
            'is_active' => $dto->isActive,
        ]);
    }

    public function update(CompanyPaymentCredential $credential, array $payload): CompanyPaymentCredential
    {
        $dto = CompanyPaymentCredentialDto::fromArray($payload);

        $credential->fill([
            'gateway_name' => $dto->name,
            'code' => $dto->code,
            'mode' => $dto->mode,
            'credentials' => $dto->credentialsArray(),
            'is_active' => $dto->isActive,
        ])->save();

        return $credential->fresh();
    }
}
