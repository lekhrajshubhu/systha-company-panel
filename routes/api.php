<?php

use Illuminate\Support\Facades\Route;
use Systha\CompanyPanel\Http\Controllers\Api\Company\CompanyAuthController;
use Systha\CompanyPanel\Http\Controllers\Api\Company\CompanyCustomerController;
use Systha\CompanyPanel\Http\Controllers\Api\Company\CompanyEmailTemplateController;
use Systha\CompanyPanel\Http\Controllers\Api\Company\CompanyInquiryController;
use Systha\CompanyPanel\Http\Controllers\Api\Company\CompanyPaymentCredentialController;
use Systha\CompanyPanel\Http\Controllers\Api\Company\CompanyServiceController;
use Systha\CompanyPanel\Http\Controllers\Api\Company\CompanyServiceQuestionController;
use Systha\CompanyPanel\Http\Controllers\Api\Company\CompanyUserController;
use Systha\CompanyPanel\Http\Controllers\Api\Company\CompanyVendorController;
use Systha\CompanyPanel\Http\Controllers\Api\Company\UserStoreController;
use Systha\CompanyPanel\Http\Controllers\Api\Company\VendorStoreController;
use Systha\Core\Http\Middleware\CompanyContextFromToken;
use Systha\Core\Http\Middleware\CompanyJwtAuth;

/*
|--------------------------------------------------------------------------
| Company API Routes (Managed by CompanyPanel Package)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function (): void {
    Route::post('company/login/password', [CompanyAuthController::class, 'passwordLoginWithoutCode']);
    Route::post('company/{company}/login/password', [CompanyAuthController::class, 'passwordLogin']);

    Route::middleware(CompanyJwtAuth::class)->group(function (): void {
        Route::post('company/logout', [CompanyAuthController::class, 'logout']);
        Route::post('company/refresh', [CompanyAuthController::class, 'refresh']);

        Route::middleware(CompanyContextFromToken::class)->prefix('company/{company}')->group(function (): void {
            Route::get('me', [CompanyAuthController::class, 'me']);
            Route::get('customers/all', [CompanyCustomerController::class, 'listAll']);
            Route::get('customers', [CompanyCustomerController::class, 'index']);
            Route::get('customers/{id}', [CompanyCustomerController::class, 'show']);
            Route::get('email-templates', [CompanyEmailTemplateController::class, 'index']);
            Route::get('email-templates/{id}', [CompanyEmailTemplateController::class, 'show']);
            Route::put('email-templates/{id}', [CompanyEmailTemplateController::class, 'update']);
            Route::post('inquiries', [CompanyInquiryController::class, 'store']);

            Route::get('users', [CompanyUserController::class, 'index']);
            Route::get('users/{id}', [CompanyUserController::class, 'show']);
            Route::put('users/{id}', [CompanyUserController::class, 'update']);
            Route::delete('users/{id}', [CompanyUserController::class, 'destroy']);
            Route::post('users', [UserStoreController::class, 'store']);


            Route::get('vendors', [CompanyVendorController::class, 'index']);
            Route::get('vendors/{id}', [CompanyVendorController::class, 'show']);
            Route::post('vendors', [VendorStoreController::class, 'store'])
                ->name('company.vendors.store');


            Route::get('payment-credentials', [CompanyPaymentCredentialController::class, 'index']);
            Route::post('payment-credentials', [CompanyPaymentCredentialController::class, 'store']);
            Route::get('payment-credentials/{id}', [CompanyPaymentCredentialController::class, 'show']);
            Route::put('payment-credentials/{id}', [CompanyPaymentCredentialController::class, 'update']);
            Route::delete('payment-credentials/{id}', [CompanyPaymentCredentialController::class, 'destroy']);
            Route::get('services', [CompanyServiceController::class, 'index']);
            Route::get('services/{id}', [CompanyServiceController::class, 'show']);
            Route::get('services/{id}/questions', [CompanyServiceQuestionController::class, 'serviceItemQuestions']);
            // Route::get('service-groups/{id}/questions', [CompanyServiceQuestionController::class, 'serviceGroupItemQuestions']);
        });
    });
});
