<?php

namespace App\Http\Controllers;

use App\Services\AgentAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AgentAnalyticsController extends Controller
{
    protected AgentAnalyticsService $analyticsService;

    public function __construct(AgentAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Track a link click
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function trackClick(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'agent_id' => 'nullable|integer|exists:agents,id',
                'referral_code' => 'nullable|string|exists:agents,referral_code',
                'utm_source' => 'nullable|string|max:100',
                'utm_medium' => 'nullable|string|max:100',
                'utm_campaign' => 'nullable|string|max:100',
                'user_agent' => 'nullable|string',
                'session_id' => 'nullable|string|max:255',
                'browser_fingerprint' => 'nullable|string|max:255',
            ]);

            // At least one identifier must be provided
            if (empty($validated['agent_id']) && empty($validated['referral_code'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Either agent_id or referral_code must be provided'
                ], 422);
            }

            // Add request context data
            $validated['ip_address'] = $request->ip();
            $validated['user_agent'] = $validated['user_agent'] ?? $request->userAgent();
            $validated['session_id'] = $validated['session_id'] ?? session()->getId();

            $click = $this->analyticsService->trackClick($validated);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $click->id,
                    'agent_id' => $click->agent_id,
                    'referral_code' => $click->referral_code,
                    'utm_source' => $click->utm_source,
                    'utm_medium' => $click->utm_medium,
                    'utm_campaign' => $click->utm_campaign,
                    'clicked_at' => $click->clicked_at->toISOString(),
                ],
                'message' => 'Click tracked successfully'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to track click',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get analytics for a specific agent
     * 
     * @param Request $request
     * @param int $agentId
     * @return JsonResponse
     */
    public function getAgentAnalytics(Request $request, int $agentId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'utm_source' => 'nullable|string',
                'utm_medium' => 'nullable|string',
                'utm_campaign' => 'nullable|string',
            ]);

            $analytics = $this->analyticsService->getAgentAnalytics($agentId, $validated);

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'message' => 'Agent analytics retrieved successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve analytics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get analytics for all agents
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllAgentsAnalytics(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'utm_source' => 'nullable|string',
                'utm_medium' => 'nullable|string',
                'utm_campaign' => 'nullable|string',
            ]);

            $analytics = $this->analyticsService->getAllAgentsAnalytics($validated);

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'message' => 'All agents analytics retrieved successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve analytics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Mark a click as converted
     * 
     * @param Request $request
     * @param int $clickId
     * @return JsonResponse
     */
    public function markConversion(Request $request, int $clickId): JsonResponse
    {
        try {
            $success = $this->analyticsService->markConversion($clickId);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Click marked as converted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Click not found or already converted'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark conversion',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Mark conversion by session and agent
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function markConversionBySession(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'session_id' => 'required|string',
                'agent_id' => 'required|integer|exists:agents,id',
            ]);

            $success = $this->analyticsService->markConversionBySession(
                $validated['session_id'],
                $validated['agent_id']
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Conversion tracked successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No matching click found for this session and agent'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark conversion',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get analytics summary for dashboard
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getDashboardSummary(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'period' => 'nullable|in:today,week,month,quarter,year',
            ]);

            $period = $validated['period'] ?? 'month';
            
            // Set date range based on period
            $filters = [];
            switch ($period) {
                case 'today':
                    $filters['start_date'] = now()->startOfDay();
                    $filters['end_date'] = now()->endOfDay();
                    break;
                case 'week':
                    $filters['start_date'] = now()->startOfWeek();
                    $filters['end_date'] = now()->endOfWeek();
                    break;
                case 'month':
                    $filters['start_date'] = now()->startOfMonth();
                    $filters['end_date'] = now()->endOfMonth();
                    break;
                case 'quarter':
                    $filters['start_date'] = now()->startOfQuarter();
                    $filters['end_date'] = now()->endOfQuarter();
                    break;
                case 'year':
                    $filters['start_date'] = now()->startOfYear();
                    $filters['end_date'] = now()->endOfYear();
                    break;
            }

            $analytics = $this->analyticsService->getAllAgentsAnalytics($filters);

            // Extract summary data
            $summary = [
                'period' => $period,
                'date_range' => $analytics['period'],
                'totals' => $analytics['totals'],
                'top_agents' => $analytics['agents']->take(5),
                'top_sources' => $analytics['top_sources'],
                'top_mediums' => $analytics['top_mediums'],
                'daily_trends' => array_slice($analytics['daily_trends'], -7), // Last 7 days
            ];

            return response()->json([
                'success' => true,
                'data' => $summary,
                'message' => 'Dashboard summary retrieved successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard summary',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
