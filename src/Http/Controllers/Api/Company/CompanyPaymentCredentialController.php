<?php

namespace Systha\CompanyPanel\Http\Controllers\Api\Company;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Systha\Core\Http\Concerns\HandlesApiResources;
use Systha\Core\Models\CompanyPaymentCredential;

class CompanyPaymentCredentialController extends Controller
{
    use HandlesApiResources;

    public function index(Request $request): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $query = CompanyPaymentCredential::query()
            ->where('company_id', $company->id)
            ->orderByDesc('id');

        $search = $this->search($request);
        if ($search !== null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        $paginator = $query->paginate($this->perPage($request))->withQueryString();
        $paginator->through(function (CompanyPaymentCredential $credential): array {
            return [
                'id' => $credential->id,
                'name' => $credential->name,
                'code' => $credential->code,
                'credentials' => $credential->credentials,
                'is_default' => (bool) $credential->is_default,
                'is_active' => (bool) $credential->is_active,
            ];
        });

        return $this->paginatedResponse($paginator);
    }
}
