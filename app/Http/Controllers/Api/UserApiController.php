<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get user's API applications
     */
    public function applications(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // In a real implementation, you would have a database table for API applications
        // For now, we'll return mock data
        $applications = [
            [
                'id' => 1,
                'name' => 'My Event Manager',
                'description' => 'Application for managing events',
                'app_id' => 'app_' . uniqid(),
                'status' => 'active',
                'created_at' => now()->subDays(30),
                'tokens' => [
                    [
                        'id' => 1,
                        'name' => 'Production Token',
                        'created_at' => now()->subDays(15),
                        'last_used' => now()->subHours(2)
                    ]
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    /**
     * Get user's API tokens
     */
    public function tokens(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Get user's API tokens
        $tokens = $user->tokens()->get(['id', 'name', 'created_at', 'last_used_at']);

        return response()->json([
            'success' => true,
            'data' => $tokens
        ]);
    }

    /**
     * Create new API token for user
     */
    public function createToken(Request $request): JsonResponse
    {
        $request->validate([
            'token_name' => 'required|string|max:255'
        ]);

        $user = $request->user();
        $token = $user->createToken($request->token_name);

        return response()->json([
            'success' => true,
            'message' => 'Token created successfully',
            'data' => [
                'token' => $token->plainTextToken,
                'abilities' => $token->accessToken->abilities,
                'created_at' => $token->accessToken->created_at
            ]
        ]);
    }

    /**
     * Revoke API token
     */
    public function revokeToken(Request $request, $tokenId): JsonResponse
    {
        $user = $request->user();
        $token = $user->tokens()->findOrFail($tokenId);
        
        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token revoked successfully'
        ]);
    }

    /**
     * Get API usage statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Mock statistics - in real implementation, track actual usage
        $stats = [
            'total_requests' => 1247,
            'requests_this_month' => 342,
            'active_tokens' => $user->tokens()->count(),
            'last_activity' => now()->subHours(2),
            'most_used_endpoint' => '/api/events',
            'rate_limit_remaining' => 8753
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
