<?php

namespace Systha\CompanyPanel\Http\Controllers\Api\Company;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Systha\Core\Http\Concerns\HandlesApiResources;
use Systha\Core\Models\VendorModel;
use Systha\CompanyPanel\Http\Resources\Api\Company\CompanyVendorResource;

class CompanyVendorController extends Controller
{
    use HandlesApiResources;

    public function index(Request $request)
    {
        /** @var \Systha\Core\Models\Company $company */
        $company = $request->get('company');

        if (!$company) {
            return response()->json(['error' => 'Company context not found'], 400);
        }

        $vendors = VendorModel::query()
            ->where('company_id', $company->id)
            ->with(['address'])
            ->orderByDesc('created_at')
            ->paginate($this->perPage($request));

        $vendors->through(fn(VendorModel $vendor) => new CompanyVendorResource($vendor));

        return $this->paginatedResponse($vendors);
    }

    public function show($id)
    {
        $vendor = VendorModel::query()->with('address')->find($id);

        if (!$vendor) {
            return response()->json(['error' => 'Vendor not found'], 404);
        }

        return new CompanyVendorResource($vendor);
    }
}
