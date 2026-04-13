<?php

namespace Systha\CompanyPanel\Http\Controllers\Api\Company;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Systha\CompanyPanel\Application\PaymentCredentials\Handlers\CredentialStoreHandler;
use Systha\CompanyPanel\Http\Requests\CredentialStoreRequest;
use Systha\Core\Http\Concerns\HandlesApiResources;
use Systha\Core\Models\CompanyPaymentCredential;

class CompanyPaymentCredentialController extends Controller
{
    use HandlesApiResources;

    public function __construct(private readonly CredentialStoreHandler $credentialStoreHandler)
    {
    }

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
                $builder->where('gateway_name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        $paginator = $query->paginate($this->perPage($request))->withQueryString();
        $paginator->through(function (CompanyPaymentCredential $credential): array {
            return $this->serializeCredential($credential);
        });

        return $this->paginatedResponse($paginator);
    }

    public function show(Request $request, string $companyId, int $id): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $credential = CompanyPaymentCredential::query()
            ->where('company_id', $company->id)
            ->find($id);

        if (!$credential) {
            return response()->json(['message' => 'Payment credential not found.'], 404);
        }

        return response()->json([
            'data' => $this->serializeCredential($credential),
        ]);
    }

    public function store(CredentialStoreRequest $request): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company context not found.',
                'data' => null,
            ], 404);
        }

        try {
            $credential = $this->credentialStoreHandler->handle($company, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Payment credential created successfully.',
                'data' => $this->serializeCredential($credential),
            ], 201);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Failed to create payment credential', [
                'company_id' => $company->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment credential.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(CredentialStoreRequest $request, string $companyId, int $id): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company context not found.',
                'data' => null,
            ], 404);
        }

        $credential = CompanyPaymentCredential::query()
            ->where('company_id', $company->id)
            ->find($id);

        if (!$credential) {
            return response()->json([
                'success' => false,
                'message' => 'Payment credential not found.',
                'data' => null,
            ], 404);
        }

        try {
            $updated = $this->credentialStoreHandler->update($credential, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Payment credential updated successfully.',
                'data' => $this->serializeCredential($updated),
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Failed to update payment credential', [
                'company_id' => $company->id ?? null,
                'credential_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment credential.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, string $companyId, int $id): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company context not found.',
                'data' => null,
            ], 404);
        }

        $credential = CompanyPaymentCredential::query()
            ->where('company_id', $company->id)
            ->find($id);

        if (!$credential) {
            return response()->json([
                'success' => false,
                'message' => 'Payment credential not found.',
                'data' => null,
            ], 404);
        }

        try {
            $credential->delete();

            return response()->json([
                'success' => true,
                'message' => 'Payment credential deleted successfully.',
                'data' => null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to delete payment credential', [
                'company_id' => $company->id ?? null,
                'credential_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment credential.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    private function serializeCredential(CompanyPaymentCredential $credential): array
    {
        return [
            'id' => $credential->id,
            'name' => $credential->gateway_name,
            'code' => $credential->code,
            'mode' => $credential->mode,
            'credentials' => $credential->credentials,
            'is_default' => (bool) $credential->is_default,
            'is_active' => (bool) $credential->is_active,
        ];
    }
}
