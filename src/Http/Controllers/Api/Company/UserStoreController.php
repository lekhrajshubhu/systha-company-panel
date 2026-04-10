<?php

namespace Systha\CompanyPanel\Http\Controllers\Api\Company;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Systha\CompanyPanel\Application\Users\Handlers\UserStoreHandler;
use Systha\CompanyPanel\Http\Requests\CompanyUserStoreRequest;

class UserStoreController extends Controller
{
    public function __construct(private readonly UserStoreHandler $userStoreHandler)
    {
    }

    public function store(CompanyUserStoreRequest $request): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company context not found.',
                'email' => null,
            ], 404);
        }

        try {
            $data = $request->validated();

            $user = $this->userStoreHandler->handle($company, $data);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'data' => $user->client->email ?? null,
            ], 201);
        } catch (ValidationException $e) {
            // rethrow to let Laravel return validation errors
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Failed to create company user', [
                'company_id' => $company->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create user.',
                'email' => null,
            ], 500);
        }
    }
}
