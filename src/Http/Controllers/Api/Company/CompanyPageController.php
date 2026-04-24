<?php

namespace Systha\CompanyPanel\Http\Controllers\Api\Company;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Systha\CompanyPanel\Application\CompanyPages\Handlers\CompanyPageStoreHandler;
use Systha\CompanyPanel\Http\Requests\CompanyPageStoreRequest;
use Systha\Core\Models\CompanyPage;

class CompanyPageController extends Controller
{
    public function __construct(private readonly CompanyPageStoreHandler $companyPageStoreHandler)
    {
    }

    public function index(): JsonResponse
    {
        $company = request()->get('company');
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company context not found.',
                'data' => [],
            ], 404);
        }

        $pages = CompanyPage::query()
            ->where('company_id', $company->id)
            ->whereNull('deleted_at')
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (CompanyPage $page) => $this->serializePage($page))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Company pages fetched successfully.',
            'data' => $pages,
        ]);
    }

    public function store(CompanyPageStoreRequest $request): JsonResponse
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
            $page = $this->companyPageStoreHandler->handle($company, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Company page created successfully.',
                'data' => $this->serializePage($page),
            ], 201);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Failed to create company page', [
                'company_id' => $company->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create company page.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(CompanyPageStoreRequest $request, string $companyId, int $id): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company context not found.',
                'data' => null,
            ], 404);
        }

        $page = CompanyPage::query()
            ->where('company_id', $company->id)
            ->find($id);

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Company page not found.',
                'data' => null,
            ], 404);
        }

        try {
            $updated = $this->companyPageStoreHandler->update($page, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Company page updated successfully.',
                'data' => $this->serializePage($updated),
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Failed to update company page', [
                'company_id' => $company->id ?? null,
                'page_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update company page.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    private function serializePage(CompanyPage $page): array
    {
        return [
            'id' => $page->id,
            'company_id' => $page->company_id,
            'title' => $page->title,
            'subtitle' => $page->subtitle,
            'slug' => $page->slug,
            'content' => $page->content,
            'status' => $page->status,
            'is_deleted' => (bool) $page->is_deleted,
            'deleted_at' => $page->deleted_at,
            'created_at' => $page->created_at,
            'updated_at' => $page->updated_at,
        ];
    }
}
