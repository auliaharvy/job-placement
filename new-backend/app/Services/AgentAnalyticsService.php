<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\AgentLinkClick;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AgentAnalyticsService
{
    /**
     * Track a link click
     */
    public function trackClick(array $data): AgentLinkClick
    {
        // Find agent by ID or referral code
        $agent = null;
        
        if (isset($data['agent_id'])) {
            $agent = Agent::find($data['agent_id']);
        } elseif (isset($data['referral_code'])) {
            $agent = Agent::where('referral_code', $data['referral_code'])->first();
        }

        if (!$agent) {
            throw new \Exception('Agent not found');
        }

        // Create click record
        return AgentLinkClick::create([
            'agent_id' => $agent->id,
            'referral_code' => $data['referral_code'] ?? $agent->referral_code,
            'utm_source' => $data['utm_source'] ?? 'direct',
            'utm_medium' => $data['utm_medium'] ?? 'direct',
            'utm_campaign' => $data['utm_campaign'] ?? null,
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'session_id' => $data['session_id'] ?? session()->getId(),
            'browser_fingerprint' => $data['browser_fingerprint'] ?? null,
            'clicked_at' => $data['clicked_at'] ?? now(),
        ]);
    }

    /**
     * Get analytics for a specific agent
     */
    public function getAgentAnalytics(int $agentId, array $filters = []): array
    {
        $agent = Agent::with('user')->findOrFail($agentId);
        
        // Date range filter
        $startDate = isset($filters['start_date']) 
            ? Carbon::parse($filters['start_date']) 
            : Carbon::now()->subDays(30);
        $endDate = isset($filters['end_date']) 
            ? Carbon::parse($filters['end_date']) 
            : Carbon::now();

        $query = $agent->linkClicks()->dateRange($startDate, $endDate);

        // Apply additional filters
        if (isset($filters['utm_source'])) {
            $query->bySource($filters['utm_source']);
        }
        if (isset($filters['utm_medium'])) {
            $query->byMedium($filters['utm_medium']);
        }
        if (isset($filters['utm_campaign'])) {
            $query->byCampaign($filters['utm_campaign']);
        }

        $clicks = $query->get();

        return [
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->user->name ?? 'Unknown',
                'agent_code' => $agent->agent_code,
                'referral_code' => $agent->referral_code,
                'success_rate' => $agent->success_rate,
                'successful_placements' => $agent->successful_placements,
                'total_referrals' => $agent->total_referrals,
            ],
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'days' => $startDate->diffInDays($endDate) + 1,
            ],
            'totals' => [
                'total_clicks' => $clicks->count(),
                'unique_clicks' => $clicks->unique(fn($click) => $click->session_id . '_' . $click->ip_address)->count(),
                'converted_clicks' => $clicks->whereNotNull('converted_at')->count(),
                'conversion_rate' => $this->calculateConversionRate($clicks),
            ],
            'sources' => $this->getClicksByDimension($clicks, 'utm_source'),
            'mediums' => $this->getClicksByDimension($clicks, 'utm_medium'),
            'campaigns' => $this->getClicksByDimension($clicks, 'utm_campaign'),
            'daily_clicks' => $this->getDailyClicks($clicks, $startDate, $endDate),
            'hourly_distribution' => $this->getHourlyDistribution($clicks),
            'top_user_agents' => $this->getTopUserAgents($clicks),
            'conversion_funnel' => $this->getConversionFunnel($agentId, $startDate, $endDate),
        ];
    }

    /**
     * Get analytics for all agents
     */
    public function getAllAgentsAnalytics(array $filters = []): array
    {
        $startDate = isset($filters['start_date']) 
            ? Carbon::parse($filters['start_date']) 
            : Carbon::now()->subDays(30);
        $endDate = isset($filters['end_date']) 
            ? Carbon::parse($filters['end_date']) 
            : Carbon::now();

        $agents = Agent::with(['user', 'linkClicks' => function ($query) use ($startDate, $endDate) {
            $query->dateRange($startDate, $endDate);
        }])->get();

        $totalClicks = AgentLinkClick::dateRange($startDate, $endDate)->count();
        $totalUniqueClicks = AgentLinkClick::dateRange($startDate, $endDate)
            ->distinct(['session_id', 'ip_address'])
            ->count();
        $totalConversions = AgentLinkClick::dateRange($startDate, $endDate)
            ->converted()
            ->count();

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'days' => $startDate->diffInDays($endDate) + 1,
            ],
            'totals' => [
                'total_agents' => $agents->count(),
                'active_agents' => $agents->where('status', 'active')->count(),
                'total_clicks' => $totalClicks,
                'unique_clicks' => $totalUniqueClicks,
                'conversions' => $totalConversions,
                'overall_conversion_rate' => $totalClicks > 0 ? round(($totalConversions / $totalClicks) * 100, 2) : 0,
            ],
            'agents' => $agents->map(function ($agent) {
                $clicks = $agent->linkClicks;
                return [
                    'id' => $agent->id,
                    'name' => $agent->user->name ?? 'Unknown',
                    'agent_code' => $agent->agent_code,
                    'referral_code' => $agent->referral_code,
                    'clicks' => $clicks->count(),
                    'unique_clicks' => $clicks->unique(fn($click) => $click->session_id . '_' . $click->ip_address)->count(),
                    'conversions' => $clicks->whereNotNull('converted_at')->count(),
                    'conversion_rate' => $this->calculateConversionRate($clicks),
                    'success_rate' => $agent->success_rate,
                    'total_placements' => $agent->successful_placements,
                ];
            })->sortByDesc('clicks')->values(),
            'top_sources' => $this->getTopSources($startDate, $endDate),
            'top_mediums' => $this->getTopMediums($startDate, $endDate),
            'daily_trends' => $this->getDailyTrends($startDate, $endDate),
        ];
    }

    /**
     * Mark a click as converted
     */
    public function markConversion(int $clickId): bool
    {
        $click = AgentLinkClick::find($clickId);
        if ($click && !$click->isConverted()) {
            $click->markAsConverted();
            return true;
        }
        return false;
    }

    /**
     * Find and mark conversion by session/agent
     */
    public function markConversionBySession(string $sessionId, int $agentId): bool
    {
        $click = AgentLinkClick::where('session_id', $sessionId)
            ->where('agent_id', $agentId)
            ->whereNull('converted_at')
            ->orderBy('clicked_at', 'desc')
            ->first();

        if ($click) {
            $click->markAsConverted();
            return true;
        }
        return false;
    }

    /**
     * Get clicks grouped by dimension
     */
    private function getClicksByDimension($clicks, string $dimension): array
    {
        return $clicks->groupBy($dimension)
            ->map(fn($group) => $group->count())
            ->sortDesc()
            ->take(10)
            ->toArray();
    }

    /**
     * Get daily clicks breakdown
     */
    private function getDailyClicks($clicks, Carbon $startDate, Carbon $endDate): array
    {
        $daily = $clicks->groupBy(fn($click) => $click->clicked_at->format('Y-m-d'))
            ->map(fn($group) => $group->count());

        // Fill missing dates with 0
        $result = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $result[$dateKey] = $daily->get($dateKey, 0);
        }

        return $result;
    }

    /**
     * Get hourly distribution
     */
    private function getHourlyDistribution($clicks): array
    {
        return $clicks->groupBy(fn($click) => $click->clicked_at->format('H'))
            ->map(fn($group) => $group->count())
            ->sortKeys()
            ->toArray();
    }

    /**
     * Get top user agents
     */
    private function getTopUserAgents($clicks): array
    {
        return $clicks->groupBy('user_agent')
            ->map(fn($group) => $group->count())
            ->sortDesc()
            ->take(5)
            ->toArray();
    }

    /**
     * Get conversion funnel data
     */
    private function getConversionFunnel(int $agentId, Carbon $startDate, Carbon $endDate): array
    {
        $agent = Agent::find($agentId);
        $clicks = AgentLinkClick::forAgent($agentId)->dateRange($startDate, $endDate)->count();
        $conversions = AgentLinkClick::forAgent($agentId)->dateRange($startDate, $endDate)->converted()->count();
        $placements = $agent->placements()->whereBetween('created_at', [$startDate, $endDate])->count();

        return [
            'clicks' => $clicks,
            'conversions' => $conversions,
            'placements' => $placements,
            'click_to_conversion_rate' => $clicks > 0 ? round(($conversions / $clicks) * 100, 2) : 0,
            'conversion_to_placement_rate' => $conversions > 0 ? round(($placements / $conversions) * 100, 2) : 0,
            'click_to_placement_rate' => $clicks > 0 ? round(($placements / $clicks) * 100, 2) : 0,
        ];
    }

    /**
     * Calculate conversion rate
     */
    private function calculateConversionRate($clicks): float
    {
        $total = $clicks->count();
        $converted = $clicks->whereNotNull('converted_at')->count();
        
        return $total > 0 ? round(($converted / $total) * 100, 2) : 0;
    }

    /**
     * Get top traffic sources
     */
    private function getTopSources(Carbon $startDate, Carbon $endDate): array
    {
        return AgentLinkClick::dateRange($startDate, $endDate)
            ->select('utm_source', DB::raw('count(*) as clicks'))
            ->groupBy('utm_source')
            ->orderByDesc('clicks')
            ->take(5)
            ->pluck('clicks', 'utm_source')
            ->toArray();
    }

    /**
     * Get top traffic mediums
     */
    private function getTopMediums(Carbon $startDate, Carbon $endDate): array
    {
        return AgentLinkClick::dateRange($startDate, $endDate)
            ->select('utm_medium', DB::raw('count(*) as clicks'))
            ->groupBy('utm_medium')
            ->orderByDesc('clicks')
            ->take(5)
            ->pluck('clicks', 'utm_medium')
            ->toArray();
    }

    /**
     * Get daily trends
     */
    private function getDailyTrends(Carbon $startDate, Carbon $endDate): array
    {
        $trends = AgentLinkClick::dateRange($startDate, $endDate)
            ->select(
                DB::raw('DATE(clicked_at) as date'),
                DB::raw('count(*) as clicks'),
                DB::raw('count(DISTINCT CONCAT(session_id, ip_address)) as unique_clicks'),
                DB::raw('sum(CASE WHEN converted_at IS NOT NULL THEN 1 ELSE 0 END) as conversions')
            )
            ->groupBy(DB::raw('DATE(clicked_at)'))
            ->orderBy('date')
            ->get();

        // Fill missing dates
        $result = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $trend = $trends->firstWhere('date', $dateKey);
            
            $result[] = [
                'date' => $dateKey,
                'clicks' => $trend->clicks ?? 0,
                'unique_clicks' => $trend->unique_clicks ?? 0,
                'conversions' => $trend->conversions ?? 0,
            ];
        }

        return $result;
    }
}
