<?php

namespace Systha\CompanyPanel\Http\Controllers\Api\Company;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Systha\CompanyPanel\Application\Vendors\Dto\CompanyVendorDto;
use Systha\CompanyPanel\Application\Vendors\Handlers\VendorStoreHandler;
use Systha\CompanyPanel\Http\Requests\CompanyVendorStoreRequest;

class VendorStoreController extends Controller
{
    public function __construct(private readonly VendorStoreHandler $handler)
    {
    }

    public function store(CompanyVendorStoreRequest $request): JsonResponse
    {
        // prefer attributes (middleware) fallback to route param
        $company = $request->get('company') ?? null;
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company context not found.',
                'vendor_email' => null,
            ], 404);
        }

    
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $dto = CompanyVendorDto::fromArray($data);

            $vendor = $this->handler->handle($company, $dto);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Vendor created successfully.',
                'vendor_email' => $vendor->email ?? null,
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to create vendor', [
                'company_id' => $company->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create vendor.'.$e->getMessage(),
                'vendor_email' => null,
            ], 500);
        }
    }
}