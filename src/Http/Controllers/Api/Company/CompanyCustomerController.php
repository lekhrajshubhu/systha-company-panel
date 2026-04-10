<?php

namespace Systha\CompanyPanel\Http\Controllers\Api\Company;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Systha\Core\Http\Concerns\HandlesApiResources;
use Systha\Core\Models\CompanyCustomer;

class CompanyCustomerController extends Controller
{
    use HandlesApiResources;

    public function index(Request $request): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $query = CompanyCustomer::query()
            ->where('company_id', $company->id)
            ->with('person')
            ->orderByDesc('id');

        $search = $this->search($request);
        if ($search !== null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('email', 'like', '%' . $search . '%')
                    ->orWhere('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhereHas('person', function ($q) use ($search): void {
                        $q->where('phone', 'like', '%' . $search . '%')
                            ->orWhere('mobile', 'like', '%' . $search . '%');
                    });
            });
        }

        $paginator = $query->paginate($this->perPage($request))->withQueryString();
        $paginator->through(function (CompanyCustomer $account): array {
            $person = $account->person;
            $name = trim(($account->first_name ?? '') . ' ' . ($account->last_name ?? ''));
            if ($name === '') {
                $name = trim(($person?->first_name ?? '') . ' ' . ($person?->last_name ?? ''));
            }

            return [
                'id' => $account->id,
                'customer_id' => $account->person_id,
                'name' => $name !== '' ? $name : null,
                'email' => $account->email,
                'phone' => $person?->phone,
                'status' => $account->status,
                'last_login_at' => optional($account->last_login_at)->toDateTimeString(),
                'created_at' => optional($account->created_at)->toDateTimeString(),
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

        $account = CompanyCustomer::query()
            ->where('company_id', $company->id)
            ->where('id', $id)
            ->with('person')
            ->first();

        if (!$account) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        $person = $account->person;
        $name = trim(($account->first_name ?? '') . ' ' . ($account->last_name ?? ''));
        if ($name === '') {
            $name = trim(($person?->first_name ?? '') . ' ' . ($person?->last_name ?? ''));
        }

        return response()->json([
            'data' => [
                'id' => $account->id,
                'customer_id' => $account->person_id,
                'name' => $name !== '' ? $name : null,
                'email' => $account->email,
                'phone' => $person?->phone,
                'status' => $account->status,
                'last_login_at' => optional($account->last_login_at)->toDateTimeString(),
            ],
        ]);
    }

    public function listAll(Request $request): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $accounts = CompanyCustomer::query()
            ->where('company_id', $company->id)
            ->with('person')
            ->orderByDesc('id')
            ->get();

        $data = $accounts->map(function (CompanyCustomer $account): array {
            $person = $account->person;
            $name = trim(($account->first_name ?? '') . ' ' . ($account->last_name ?? ''));
            if ($name === '') {
                $name = trim(($person?->first_name ?? '') . ' ' . ($person?->last_name ?? ''));
            }

            return [
                'id' => $account->id,
                'customer_id' => $account->person_id,
                'name' => $name !== '' ? $name : null,
                'avatar' => $person?->meta['avatar'] ?? null,
            ];
        });

        return response()->json([
            'data' => $data,
        ]);
    }
}
