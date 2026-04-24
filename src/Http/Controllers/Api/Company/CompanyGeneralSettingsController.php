<?php

namespace Systha\CompanyPanel\Http\Controllers\Api\Company;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Systha\CompanyPanel\Application\Company\Handlers\CompanyUpdateHandler;
use Systha\CompanyPanel\Http\Requests\CompanyGeneralSettingsRequest;
use Systha\CompanyPanel\Http\Requests\CompanyLogoUpdateRequest;

class CompanyGeneralSettingsController extends Controller
{
    public function __construct(private readonly CompanyUpdateHandler $companyUpdateHandler)
    {
    }

    public function show(Request $request): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $address = $company->address;

        $meta = $this->normalizeMeta($company->meta ?? null);
        $logoPath = $meta['logo_path'] ?? null;

        return response()->json([
            'data' => [
                'id' => $company->id,
                'company_name' => $company->c_name ?? '',
                'company_email' => $company->email ?? '',
                'company_phone' => $company->phone ?? '',
                'logo_url' => $this->resolveLogoUrl($logoPath),
                'address' => [
                    'id' => $address->id ?? null,
                    'line_1' => $address->add1 ?? '',
                    'line_2' => $address->add2 ?? '',
                    'city' => $address->city ?? '',
                    'state' => $address->state ?? '',
                    'zip' => $address->zip ?? '',
                ],
            ],
        ]);
    }

    public function update(CompanyGeneralSettingsRequest $request, string $companyId): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $payload = $request->validated();
        DB::transaction(function () use ($company, $payload): void {
            $company = $this->companyUpdateHandler->handle($company, $payload);

            if (array_key_exists('address', $payload)) {
                $addressPayload = $payload['address'] ?? [];
                $addressData = [
                    'add1' => $addressPayload['line_1'] ?? '',
                    'add2' => $addressPayload['line_2'] ?? '',
                    'city' => $addressPayload['city'] ?? '',
                    'state' => $addressPayload['state'] ?? '',
                    'zip' => $addressPayload['zip'] ?? '',
                    'is_default' => true,
                ];

                $address = $company->address()->first();

                if ($address) {
                    $address->update($addressData);
                } else {
                    $company->address()->create($addressData);
                }
            }
        });

        return $this->show($request);
    }

    public function updateLogo(CompanyLogoUpdateRequest $request, string $companyId): JsonResponse
    {
        $company = $request->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company context not found.'], 404);
        }

        $payload = $request->validated();

        DB::transaction(function () use ($company, $request, $payload): void {
            $meta = $this->normalizeMeta($company->meta ?? null);
            $existingLogoPath = $meta['logo_path'] ?? null;

            if ($request->hasFile('logo')) {
                $storedPath = $request->file('logo')->store('company-logos/' . $company->id, 'public');
                $meta['logo_path'] = $storedPath;
                $this->deleteLogo($existingLogoPath);
            } elseif (($payload['remove_logo'] ?? false) === true) {
                unset($meta['logo_path']);
                $this->deleteLogo($existingLogoPath);
            }

            $company->meta = json_encode($meta);
            $company->save();
        });

        return $this->show($request);
    }

    private function normalizeMeta(mixed $meta): array
    {
        if (is_array($meta)) {
            return $meta;
        }

        if (is_string($meta) && $meta !== '') {
            $decoded = json_decode($meta, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    private function resolveLogoUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    private function deleteLogo(?string $path): void
    {
        if (!$path || filter_var($path, FILTER_VALIDATE_URL)) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
