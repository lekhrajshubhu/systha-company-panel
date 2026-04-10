<?php

namespace Systha\CompanyPanel\Application\Users\Handlers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Systha\CompanyPanel\Application\Users\Dto\AddressDto;
use Systha\CompanyPanel\Application\Users\Dto\ClientDto;
use Systha\CompanyPanel\Application\Users\Services\AddressService;
use Systha\CompanyPanel\Application\Users\Services\ClientService;
use Systha\Core\Models\Company;
use Systha\Core\Models\CompanyUser;
use Systha\Core\Models\RoleModel;

class UserStoreHandler
{
    public function __construct(
        private readonly ClientService $clientService,
        private readonly AddressService $addressService
    ) {
    }

    public function handle(Company $company, array $payload): CompanyUser
    {
        $clientDto = ClientDto::fromArray($payload['contact'] ?? []);
        $addressDto = AddressDto::fromArray($payload['address'] ?? []);

        $role = RoleModel::query()->where('label', 'staff')->first();
        if (!$role) {
            throw ValidationException::withMessages([
                'role' => ['Default role `staff` was not found.'],
            ]);
        }

        return DB::transaction(function () use ($company, $clientDto, $addressDto, $role): CompanyUser {

            $clientResult = $this->clientService->createOrUpdate($clientDto);
            
            $client = $clientResult['client'];
            
            $this->addressService->createOrUpdateDefault($client, $addressDto);

            $email = (string) $clientDto->email;

            $user = CompanyUser::query()->create([
                'company_id' => $company->id,
                'client_id' => $client->id,
                'email' => $email,
                'password' => Hash::make(Str::random(20)),
                'role_id' => $role->id,
                'is_primary' => false,
            ]);

            return $user->load(['role', 'client']);
        });
    }
}
