<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Services\AgentAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AgentController extends Controller
{
    protected AgentAnalyticsService $analyticsService;

    public function __construct(AgentAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get all agents
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Agent::with(['user:id,first_name,last_name,email,phone']);

            // Apply search filter
            if ($request->has('search') && !empty($request->search)) {
                $query->search($request->search);
            }

            // Apply status filter
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            // Apply level filter
            if ($request->has('level') && !empty($request->level)) {
                $query->level($request->level);
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');

            $allowedSortFields = ['created_at', 'agent_code', 'success_rate', 'successful_placements', 'total_points', 'total_commission'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortDirection);
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);

            if ($request->has('paginate') && $request->paginate === 'false') {
                $agents = $query->get();
                $result = $agents;
            } else {
                $result = $query->paginate($perPage);
                $agents = $result->items();
            }

            // Transform the data
            $transformedAgents = collect($agents)->map(function ($agent) {
                $full_name = $agent->user->first_name. ' '. $agent->user->last_name;
                return [
                    'id' => $agent->id,
                    'agent_code' => $agent->agent_code,
                    'referral_code' => $agent->referral_code,
                    'level' => $agent->level,
                    'total_referrals' => $agent->total_referrals,
                    'successful_placements' => $agent->successful_placements,
                    'success_rate' => number_format($agent->success_rate, 1),
                    'total_points' => $agent->total_points,
                    'total_commission' => 'Rp ' . number_format($agent->total_commission, 0, ',', '.'),
                    'status' => $agent->status,
                    'qr_code_url' => $agent->qr_code_url,
                    'user' => [
                        'id' => $agent->user->id ?? null,
                        'first_name' => $agent->user->first_name ?? '',
                        'last_name' => $agent->user->last_name ?? '',
                        'full_name' => $full_name ?? 'Unknown',
                        'email' => $agent->user->email ?? '',
                        'phone' => $agent->user->phone ?? '',
                    ],
                    'created_at' => $agent->created_at->toISOString(),
                    'updated_at' => $agent->updated_at->toISOString(),
                ];
            });

            if ($request->has('paginate') && $request->paginate === 'false') {
                return response()->json([
                    'success' => true,
                    'data' => $transformedAgents,
                    'message' => 'Agents retrieved successfully'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $transformedAgents,
                'meta' => [
                    'current_page' => $result->currentPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                    'last_page' => $result->lastPage(),
                    'from' => $result->firstItem(),
                    'to' => $result->lastItem(),
                ],
                'message' => 'Agents retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve agents',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get agent by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $agent = Agent::with(['user:id,first_name,last_name,email,phone'])->findOrFail($id);

            $full_name = $agent->user->first_name. ' '. $agent->user->last_name;
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $agent->id,
                    'agent_code' => $agent->agent_code,
                    'referral_code' => $agent->referral_code,
                    'level' => $agent->level,
                    'total_referrals' => $agent->total_referrals,
                    'successful_placements' => $agent->successful_placements,
                    'success_rate' => number_format($agent->success_rate, 1),
                    'total_points' => $agent->total_points,
                    'total_commission' => 'Rp ' . number_format($agent->total_commission, 0, ',', '.'),
                    'status' => $agent->status,
                    'qr_code_url' => $agent->qr_code_url,
                    'user' => [
                        'id' => $agent->user->id ?? null,
                        'first_name' => $agent->user->first_name ?? '',
                        'last_name' => $agent->user->last_name ?? '',
                        'full_name' => $full_name ?? 'Unknown',
                        'email' => $agent->user->email ?? '',
                        'phone' => $agent->user->phone ?? '',
                    ],
                    'performance_metrics' => $agent->performance_metrics,
                    'created_at' => $agent->created_at->toISOString(),
                    'updated_at' => $agent->updated_at->toISOString(),
                ],
                'message' => 'Agent retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found',
                'error' => config('app.debug') ? $e->getMessage() : 'Agent not found'
            ], 404);
        }
    }

    /**
     * Get agent by referral code
     *
     * @param string $referralCode
     * @return JsonResponse
     */
    public function getByReferralCode(string $referralCode): JsonResponse
    {
        try {
            $agent = Agent::with(['user:id,first_name,last_name,email,phone'])
                ->where('referral_code', $referralCode)
                ->firstOrFail();

            $full_name = $agent->user->first_name. ' '. $agent->user->last_name;
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $agent->id,
                    'agent_code' => $agent->agent_code,
                    'referral_code' => $agent->referral_code,
                    'level' => $agent->level,
                    'total_referrals' => $agent->total_referrals,
                    'successful_placements' => $agent->successful_placements,
                    'success_rate' => number_format($agent->success_rate, 1),
                    'total_points' => $agent->total_points,
                    'total_commission' => 'Rp ' . number_format($agent->total_commission, 0, ',', '.'),
                    'status' => $agent->status,
                    'qr_code_url' => $agent->qr_code_url,
                    'user' => [
                        'id' => $agent->user->id ?? null,
                        'first_name' => $agent->user->first_name ?? '',
                        'last_name' => $agent->user->last_name ?? '',
                        'full_name' => $full_name,
                        'email' => $agent->user->email ?? '',
                        'phone' => $agent->user->phone ?? '',
                    ],
                    'created_at' => $agent->created_at->toISOString(),
                    'updated_at' => $agent->updated_at->toISOString(),
                ],
                'message' => 'Agent found successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found with referral code: ' . $referralCode,
                'error' => config('app.debug') ? $e->getMessage() : 'Agent not found'
            ], 404);
        }
    }
}
