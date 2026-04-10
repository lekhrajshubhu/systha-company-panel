<?php

namespace Systha\CompanyPanel\Http\Controllers\Api\Company;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Systha\Core\Http\Concerns\HandlesApiResources;
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
                'role' => $member->role?->name,
                'status' => $member->status,
                'last_login_at' => optional($member->last_login_at)->toDateTimeString(),
            ];
        });

        return $this->paginatedResponse($paginator);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $member = CompanyUser::query()
            ->select('company_users.*', 'clients.fname', 'clients.lname', 'clients.email as client_email')
            ->join('clients', 'company_users.client_id', '=', 'clients.id')
            ->where('company_users.company_id', $company->id)
            ->where('company_users.id', $id)
            ->with('role')
            ->first();

        if (!$member) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $name = trim(($member->fname ?? '') . ' ' . ($member->lname ?? ''));

        return response()->json([
            'data' => [
                'id' => $member->id,
                'name' => $name !== '' ? $name : null,
                'email' => $member->client_email ?? $member->email,
                'role' => $member->role?->name,
                'status' => $member->status,
                'last_login_at' => optional($member->last_login_at)->toDateTimeString(),
            ],
        ]);
    }
}
