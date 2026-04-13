<?php

namespace Systha\CompanyPanel\Http\Controllers\Api\Company;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Systha\CompanyPanel\Application\Users\Dto\AddressDto;
use Systha\CompanyPanel\Application\Users\Dto\ClientDto;
use Systha\CompanyPanel\Http\Requests\CompanyUserUpdateRequest;
use Systha\Core\Http\Concerns\HandlesApiResources;
use Systha\Core\Models\AddressModel;
use Systha\Core\Models\CompanyUser;

class CompanyUserController extends Controller
{
    use HandlesApiResources;

    public function index(Request $request): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $query = CompanyUser::query()
            ->select('company_users.*', 'clients.fname', 'clients.lname', 'clients.email as client_email')
            ->join('clients', 'company_users.client_id', '=', 'clients.id')
            ->where('company_users.company_id', $company->id)
            ->with(['role'])
            ->orderByDesc('company_users.id');

        $search = $this->search($request);
        if ($search !== null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('clients.fname', 'like', '%' . $search . '%')
                    ->orWhere('clients.lname', 'like', '%' . $search . '%')
                    ->orWhere('clients.email', 'like', '%' . $search . '%');
            });
        }

        $paginator = $query->paginate($this->perPage($request))->withQueryString();
        $paginator->through(function ($member): array {
            $name = trim(($member->fname ?? '') . ' ' . ($member->lname ?? ''));

            return [
                'id' => $member->id,
                'name' => $name !== '' ? $name : null,
                'email' => $member->client_email ?? $member->email,
                'role' => $member->role?->role_name,
                'status' => $member->status,
                'joined_at' => optional($member->created_at)->toDateTimeString(),
                'last_login_at' => optional($member->last_login_at)->toDateTimeString(),
            ];
        });

        return $this->paginatedResponse($paginator);
    }

    public function show(Request $request, $companyId, $id): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $member = CompanyUser::query()
            ->where('company_users.company_id', $company->id)
            ->where('company_users.id', $id)
            ->with('client')
            ->first();

        if (!$member) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $client = $member->client;

        $address = null;
        if ($client) {
            $address = AddressModel::query()
                ->where('table_name', 'clients')
                ->where('table_id', $client->id)
                ->where('is_default', true)
                ->first();
        }

        return response()->json([
            'data' => [
                'id' => $member->id,
                'contact' => [
                    'id' => $client->id ?? null,
                    'first_name' => $client->fname ?? '',
                    'last_name' => $client->lname ?? '',
                    'email' => $client->email ?? $member->email ?? '',
                    'phone1' => $client->phone1 ?? '',
                ],
                'address' => [
                    'id' => $address->id ?? null,
                    'line1' => $address->add1 ?? '',
                    'line2' => $address->add2 ?? '',
                    'city' => $address->city ?? '',
                    'state' => $address->state ?? '',
                    'zip' => $address->zip ?? '',
                    'lat' => $address->lat ?? null,
                    'lng' => $address->lon ?? null,
                ],
            ],
        ]);
    }

    public function update(CompanyUserUpdateRequest $request, $companyId, $id): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $member = CompanyUser::query()
            ->where('company_users.company_id', $company->id)
            ->where('company_users.id', $id)
            ->with('client')
            ->first();

        if (!$member) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $client = $member->client;
        if (!$client) {
            return response()->json(['message' => 'Client not found for user.'], 404);
        }

        $payload = $request->validated();
        $contact = ClientDto::fromArray($payload['contact'] ?? []);
        $addressDto = AddressDto::fromArray($payload['address'] ?? []);

        DB::transaction(function () use ($member, $client, $contact, $addressDto): void {
            $client->update([
                'fname' => $contact->fname,
                'lname' => $contact->lname,
                'email' => $contact->email,
                'phone1' => $contact->phone1,
            ]);

            $member->update([
                'email' => $contact->email,
            ]);

            $addressData = $addressDto->toArray();
            $address = AddressModel::query()
                ->where('table_name', 'clients')
                ->where('table_id', $client->id)
                ->where('is_default', true)
                ->first();

            if ($address) {
                $address->update($addressData);
            } else {
                AddressModel::query()->create([
                    ...$addressData,
                    'table_name' => 'clients',
                    'table_id' => $client->id,
                    'is_default' => true,
                ]);
            }
        });

        return $this->show($request, $companyId, $id);
    }

    public function destroy(Request $request, $companyId, $id): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $member = CompanyUser::query()
            ->where('company_users.company_id', $company->id)
            ->where('company_users.id', $id)
            ->first();

        if (!$member) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }
}
