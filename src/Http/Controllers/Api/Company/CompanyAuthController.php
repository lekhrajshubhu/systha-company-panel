<?php

namespace Systha\CompanyPanel\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Systha\Core\Models\Company;
use Systha\Core\Models\CompanyUser;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompanyAuthController extends Controller
{
    /**
     * Password login for a specific company.
     */
    public function passwordLogin(Request $request, string $companyCode): JsonResponse
    {
        return $this->performLogin($request, $companyCode);
    }

    /**
     * Password login without a company code (resolved from email).
     */
    public function passwordLoginWithoutCode(Request $request): JsonResponse
    {
        return $this->performLogin($request);
    }

    /**
     * Shared login logic.
     */
    private function performLogin(Request $request, ?string $companyCode = null): JsonResponse
    {
        $request->validate([
            'login_identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $query = CompanyUser::where('email', $request->login_identifier)
            ->with(['company', 'role']);

        if ($companyCode) {
            $company = Company::where('code', $companyCode)->first();
            if (!$company) {
                return response()->json(['error' => 'Company not found'], 404);
            }
            $query->where('company_id', $company->id);
        }

        $member = $query->first();

        if (!$member || !$member->company) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $company = $member->company;

        // Verify password
        if (!Hash::check($request->password, $member->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Generate JWT
        $token = JWTAuth::claims([
            'company_id' => $company->id,
            'member_id' => $member->id,
            'company_code' => $company->code,
            'role' => $member->role?->name,
        ])->fromUser($member);

        $member->update(['last_login_at' => now()]);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => [
                'id' => $member->id,
                'role' => $member->role?->name,
                'name' => $member->username,
                'email' => $member->email,
                'company' => [
                    'name' => $company->name,
                    'code' => $company->code,
                ],
            ],
        ]);
    }

    /**
     * Log the account out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        return response()->json([
            'access_token' => JWTAuth::refresh(),
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ]);
    }

    /**
     * Get the authenticated User.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => [
                'id' => $request->user->id,
                'role' => $request->role,
                'name' => $request->user->username,
                'email' => $request->user->email,
                'company' => [
                    'name' => $request->company->name,
                    'code' => $request->company->code,
                ],
            ],
        ]);
    }
}
