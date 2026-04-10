<?php

namespace Systha\CompanyPanel\Http\Controllers\Api\Company;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CompanyVendorController extends Controller
{
    public function index(Request $request)
    {
        // Placeholder: return paginated vendor list
        return response()->json([
            'data' => [],
            'meta' => ['total' => 0],
        ]);
    }

    public function show($id)
    {
        // Placeholder: return vendor detail
        return response()->json([
            'data' => null,
        ]);
    }
}
