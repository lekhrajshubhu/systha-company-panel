<?php

namespace Systha\CompanyPanel\Application\Company\Handlers;

use Systha\CompanyPanel\Application\Company\Dto\CompanyDto;
use Systha\Core\Models\Company;

class CompanyUpdateHandler
{
    public function handle(Company $company, array $payload): Company
    {
        $dto = CompanyDto::fromArray($payload);

        if ($dto->hasName) {
            $company->c_name = $dto->name;
        }

        if ($dto->hasEmail) {
            $company->email = $dto->email;
        }

        if ($dto->hasEmailAlt) {
            $company->email_alt = $dto->emailAlt;
        }

        if ($dto->hasPhone) {
            $company->phone = $dto->phone;
        }

        if ($dto->hasPhoneAlt) {
            $company->phone_alt = $dto->phoneAlt;
        }

        if ($dto->hasUrl) {
            $company->url = $dto->url;
        }

        if ($dto->hasRegNo) {
            $company->reg_no = $dto->regNo;
        }

        if ($dto->hasMeta) {
            $existingMeta = $this->normalizeMeta($company->meta ?? null);
            $incomingMeta = $dto->meta ?? [];
            $company->meta = json_encode(array_merge($existingMeta, $incomingMeta));
        }

        $company->save();

        return $company->fresh() ?? $company;
    }

    private function normalizeMeta(mixed $meta): array
    {
        if (is_array($meta)) {
            return $meta;
        }

        if (is_string($meta) && $meta !== '') {
            $decoded = json_decode($meta, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }
}
