<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Applicant;
use App\Models\JobPosting;
use App\Models\Application;
use App\Models\Placement;
use App\Models\Agent;
use App\Models\Company;
use App\Models\WhatsAppLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get executive dashboard overview
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Get date range from request (default to current month)
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $dashboardData = [
            'overview' => $this->getOverviewStats($startDate, $endDate),
            'charts' => $this->getChartData($startDate, $endDate),
            'recent_activities' => $this->getRecentActivities(),
            'alerts' => $this->getSystemAlerts(),
        ];

        // Add role-specific data
        switch ($user->role) {
            case 'direktur':
            case 'super_admin':
                $dashboardData['executive_metrics'] = $this->getExecutiveMetrics($startDate, $endDate);
                break;
            
            case 'hr_staff':
                $dashboardData['hr_metrics'] = $this->getHRMetrics($startDate, $endDate);
                break;
                
            case 'agent':
                if ($user->agent) {
                    $dashboardData['agent_metrics'] = $this->getAgentMetrics($user->agent, $startDate, $endDate);
                }
                break;
                
            case 'applicant':
                if ($user->applicant) {
                    $dashboardData['applicant_metrics'] = $this->getApplicantMetrics($user->applicant);
                }
                break;
        }

        return response()->json([
            'success' => true,
            'data' => $dashboardData,
        ]);
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats(string $startDate, string $endDate): array
    {
        return [
            'total_applicants' => Applicant::count(),
            'active_applicants' => Applicant::where('work_status', 'available')->count(),
            'new_applicants_this_period' => Applicant::whereBetween('created_at', [$startDate, $endDate])->count(),
            
            'total_job_postings' => JobPosting::count(),
            'active_job_postings' => JobPosting::where('status', 'active')->count(),
            'new_jobs_this_period' => JobPosting::whereBetween('created_at', [$startDate, $endDate])->count(),
            
            'total_applications' => Application::count(),
            'active_applications' => Application::whereIn('status', ['active', 'in_progress'])->count(),
            'new_applications_this_period' => Application::whereBetween('applied_at', [$startDate, $endDate])->count(),
            
            'total_placements' => Placement::count(),
            'active_placements' => Placement::where('status', 'active')->count(),
            'new_placements_this_period' => Placement::whereBetween('start_date', [$startDate, $endDate])->count(),
            
            'total_agents' => Agent::count(),
            'active_agents' => Agent::where('status', 'active')->count(),
            
            'total_companies' => Company::count(),
            'active_companies' => Company::where('status', 'active')->count(),
        ];
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData(string $startDate, string $endDate): array
    {
        return [
            'applicants_trend' => $this->getApplicantsTrend($startDate, $endDate),
            'applications_pipeline' => $this->getApplicationsPipeline(),
            'placements_by_company' => $this->getPlacementsByCompany(),
            'whatsapp_delivery_stats' => $this->getWhatsAppStats(),
            'agent_performance' => $this->getAgentPerformance(),
            'job_success_rate' => $this->getJobSuccessRate(),
        ];
    }

    /**
     * Get applicants registration trend
     */
    private function getApplicantsTrend(string $startDate, string $endDate): array
    {
        // Use database-agnostic date functions
        $dateFormat = $this->getDateFormat();
        
        $data = Applicant::selectRaw("{$dateFormat}(created_at) as date, COUNT(*) as count")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw("{$dateFormat}(created_at)"))
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map->count;

        // Fill missing dates with zero
        $period = Carbon::parse($startDate)->daysUntil(Carbon::parse($endDate));
        $chartData = [];
        
        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $chartData[] = [
                'date' => $dateStr,
                'count' => $data->get($dateStr, 0),
                'formatted_date' => $date->format('M d'),
            ];
        }

        return $chartData;
    }

    /**
     * Get applications pipeline data
     */
    private function getApplicationsPipeline(): array
    {
        $pipeline = Application::selectRaw('current_stage, COUNT(*) as count')
            ->groupBy('current_stage')
            ->pluck('count', 'current_stage');

        $stageLabels = [
            'applied' => 'Applied',
            'screening' => 'Screening',
            'psikotes' => 'Psikotes',
            'interview' => 'Interview',
            'medical' => 'Medical',
            'final_review' => 'Final Review',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
        ];

        $data = [];
        foreach ($stageLabels as $stage => $label) {
            $data[] = [
                'stage' => $stage,
                'label' => $label,
                'count' => $pipeline->get($stage, 0),
            ];
        }

        return $data;
    }

    /**
     * Get placements by company
     */
    private function getPlacementsByCompany(): array
    {
        return Placement::join('companies', 'placements.company_id', '=', 'companies.id')
            ->selectRaw('companies.name as company_name, COUNT(*) as count')
            ->groupBy('companies.id', 'companies.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'company_name' => $item->company_name ?? 'Unknown',
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    /**
     * Get WhatsApp delivery statistics
     */
    private function getWhatsAppStats(): array
    {
        $stats = WhatsAppLog::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $total = $stats->sum();
        $delivered = $stats->get('delivered', 0) + $stats->get('read', 0);
        
        return [
            'total_sent' => $total,
            'delivered' => $delivered,
            'failed' => $stats->get('failed', 0),
            'pending' => $stats->get('pending', 0),
            'delivery_rate' => $total > 0 ? round(($delivered / $total) * 100, 2) : 0,
            'breakdown' => $stats->toArray(),
        ];
    }

    /**
     * Get agent performance data
     */
    private function getAgentPerformance(): array
    {
        return Agent::join('users', 'agents.user_id', '=', 'users.id')
            ->select('agents.id', 'agents.agent_code', 'agents.total_referrals', 
                    'agents.successful_placements', 'agents.success_rate', 'agents.level',
                    DB::raw("CONCAT(users.first_name, ' ', users.last_name) as full_name"))
            ->orderByDesc('agents.total_points')
            ->limit(10)
            ->get()
            ->map(function ($agent) {
                return [
                    'name' => $agent->full_name,
                    'agent_code' => $agent->agent_code,
                    'total_referrals' => $agent->total_referrals,
                    'successful_placements' => $agent->successful_placements,
                    'success_rate' => $agent->success_rate ?? 0,
                    'level' => $agent->level,
                ];
            })
            ->toArray();
    }

    /**
     * Get job success rate data
     */
    private function getJobSuccessRate(): array
    {
        return JobPosting::join('companies', 'job_postings.company_id', '=', 'companies.id')
            ->selectRaw('job_postings.id, job_postings.title, companies.name as company_name, 
                        job_postings.total_applications, job_postings.total_hired, 
                        CASE 
                            WHEN job_postings.total_applications > 0 
                            THEN (job_postings.total_hired::float / job_postings.total_applications * 100) 
                            ELSE 0 
                        END as success_rate')
            ->where('job_postings.total_applications', '>', 0)
            ->orderByDesc('success_rate')
            ->limit(10)
            ->get()
            ->map(function ($job) {
                return [
                    'job_title' => $job->title,
                    'company_name' => $job->company_name ?? 'Unknown',
                    'total_applications' => $job->total_applications ?? 0,
                    'total_hired' => $job->total_hired ?? 0,
                    'success_rate' => round($job->success_rate ?? 0, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities(): array
    {
        $activities = collect();

        // Recent applicant registrations
        $recentApplicants = Applicant::join('users', 'applicants.user_id', '=', 'users.id')
            ->select('applicants.*', DB::raw("CONCAT(users.first_name, ' ', users.last_name) as full_name"))
            ->orderBy('applicants.created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($applicant) {
                return [
                    'type' => 'applicant_registered',
                    'message' => "New applicant {$applicant->full_name} registered",
                    'details' => [
                        'applicant_name' => $applicant->full_name,
                        'education_level' => $applicant->education_level,
                        'city' => $applicant->city,
                    ],
                    'timestamp' => $applicant->created_at,
                    'icon' => 'user-plus',
                    'color' => 'green',
                ];
            });

        // Recent job postings
        $recentJobs = JobPosting::join('companies', 'job_postings.company_id', '=', 'companies.id')
            ->select('job_postings.*', 'companies.name as company_name')
            ->orderBy('job_postings.created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($job) {
                return [
                    'type' => 'job_posted',
                    'message' => "New job '{$job->title}' posted by {$job->company_name}",
                    'details' => [
                        'job_title' => $job->title,
                        'company_name' => $job->company_name,
                        'employment_type' => $job->employment_type,
                        'priority' => $job->priority,
                    ],
                    'timestamp' => $job->created_at,
                    'icon' => 'briefcase',
                    'color' => 'blue',
                ];
            });

        // Recent placements
        $recentPlacements = Placement::join('applicants', 'placements.applicant_id', '=', 'applicants.id')
            ->join('users', 'applicants.user_id', '=', 'users.id')
            ->join('companies', 'placements.company_id', '=', 'companies.id')
            ->select('placements.*', 
                    DB::raw("CONCAT(users.first_name, ' ', users.last_name) as applicant_name"),
                    'companies.name as company_name')
            ->orderBy('placements.start_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($placement) {
                return [
                    'type' => 'placement_created',
                    'message' => "{$placement->applicant_name} placed at {$placement->company_name}",
                    'details' => [
                        'applicant_name' => $placement->applicant_name,
                        'company_name' => $placement->company_name,
                        'position_title' => $placement->position_title,
                        'contract_type' => $placement->contract_type,
                    ],
                    'timestamp' => $placement->start_date,
                    'icon' => 'check-circle',
                    'color' => 'green',
                ];
            });

        // Combine and sort activities
        $activities = $activities->concat($recentApplicants)
                                ->concat($recentJobs)
                                ->concat($recentPlacements)
                                ->sortByDesc('timestamp')
                                ->take(15)
                                ->values();

        return $activities->toArray();
    }

    /**
     * Get system alerts
     */
    private function getSystemAlerts(): array
    {
        $alerts = [];

        // Expiring contracts alert
        $expiringContracts = Placement::where('end_date', '<=', now()->addDays(30))
                                    ->where('status', 'active')
                                    ->count();
        if ($expiringContracts > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Contracts Expiring Soon',
                'message' => "{$expiringContracts} contracts will expire in the next 30 days",
                'action_url' => '/placements?filter=expiring',
                'icon' => 'clock',
            ];
        }

        // Urgent job postings
        $urgentJobs = JobPosting::where('status', 'active')
                               ->where('priority', 'urgent')
                               ->count();
        if ($urgentJobs > 0) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'Urgent Job Postings',
                'message' => "{$urgentJobs} urgent job postings need immediate attention",
                'action_url' => '/jobs?filter=urgent',
                'icon' => 'exclamation-triangle',
            ];
        }

        // Failed WhatsApp messages
        $failedMessages = WhatsAppLog::where('status', 'failed')
                                   ->where('created_at', '>=', now()->subDays(7))
                                   ->count();
        if ($failedMessages > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'WhatsApp Delivery Issues',
                'message' => "{$failedMessages} WhatsApp messages failed to deliver in the last 7 days",
                'action_url' => '/whatsapp-logs?status=failed',
                'icon' => 'message-circle',
            ];
        }

        // Pending applications review
        $pendingApplications = Application::where('current_stage', 'applied')
                                         ->where('applied_at', '<=', now()->subDays(3))
                                         ->count();
        if ($pendingApplications > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Pending Application Reviews',
                'message' => "{$pendingApplications} applications have been waiting for review for more than 3 days",
                'action_url' => '/applications?stage=applied',
                'icon' => 'file-text',
            ];
        }

        return $alerts;
    }

    /**
     * Get executive-specific metrics
     */
    private function getExecutiveMetrics(string $startDate, string $endDate): array
    {
        return [
            'revenue_metrics' => [
                'total_placements_value' => Placement::whereBetween('start_date', [$startDate, $endDate])
                                                   ->sum('salary') ?? 0,
                'total_commission_paid' => Agent::sum('total_commission') ?? 0,
                'avg_placement_salary' => Placement::whereBetween('start_date', [$startDate, $endDate])
                                                 ->avg('salary') ?? 0,
            ],
            'growth_metrics' => [
                'applicant_growth_rate' => $this->calculateGrowthRate('applicants', $startDate, $endDate),
                'placement_growth_rate' => $this->calculateGrowthRate('placements', $startDate, $endDate),
                'company_growth_rate' => $this->calculateGrowthRate('companies', $startDate, $endDate),
            ],
            'efficiency_metrics' => [
                'avg_time_to_placement' => $this->calculateAverageTimeToPlacement(),
                'application_to_placement_ratio' => $this->calculateApplicationToPlacementRatio(),
                'top_performing_agents' => $this->getTopPerformingAgents(),
            ],
        ];
    }

    /**
     * Get HR-specific metrics
     */
    private function getHRMetrics(string $startDate, string $endDate): array
    {
        return [
            'workload_metrics' => [
                'applications_pending_review' => Application::where('current_stage', 'applied')->count(),
                'interviews_scheduled_today' => Application::whereDate('interview_scheduled_at', today())->count(),
                'job_postings_draft' => JobPosting::where('status', 'draft')->count(),
            ],
            'performance_metrics' => [
                'applications_processed_this_period' => Application::whereBetween('applied_at', [$startDate, $endDate])->count(),
                'placements_made_this_period' => Placement::whereBetween('start_date', [$startDate, $endDate])->count(),
                'avg_application_processing_time' => $this->calculateAverageProcessingTime(),
            ],
            'upcoming_tasks' => [
                'interviews_this_week' => Application::whereBetween('interview_scheduled_at', [now(), now()->addWeek()])->count(),
                'contracts_expiring_this_month' => Placement::where('end_date', '<=', now()->addDays(30))->count(),
                'job_deadlines_this_week' => JobPosting::whereBetween('application_deadline', [now(), now()->addWeek()])->count(),
            ],
        ];
    }

    /**
     * Get agent-specific metrics
     */
    private function getAgentMetrics(Agent $agent, string $startDate, string $endDate): array
    {
        return [
            'personal_stats' => [
                'total_referrals' => $agent->total_referrals ?? 0,
                'successful_placements' => $agent->successful_placements ?? 0,
                'success_rate' => $agent->success_rate ?? 0,
                'current_level' => $agent->level ?? 'bronze',
                'total_points' => $agent->total_points ?? 0,
                'total_commission' => $agent->total_commission ?? 0,
            ],
            'period_performance' => [
                'new_referrals' => Applicant::where('agent_id', $agent->id)
                                          ->whereBetween('created_at', [$startDate, $endDate])
                                          ->count(),
                'new_placements' => Placement::where('agent_id', $agent->id)
                                            ->whereBetween('start_date', [$startDate, $endDate])
                                            ->count(),
                'commission_earned' => Placement::where('agent_id', $agent->id)
                                               ->whereBetween('start_date', [$startDate, $endDate])
                                               ->sum('agent_commission') ?? 0,
            ],
        ];
    }

    /**
     * Get applicant-specific metrics
     */
    private function getApplicantMetrics(Applicant $applicant): array
    {
        return [
            'profile_status' => [
                'work_status' => $applicant->work_status ?? 'available',
                'profile_completion_percentage' => $this->calculateProfileCompletion($applicant),
            ],
            'application_history' => [
                'total_applications' => Application::where('applicant_id', $applicant->id)->count(),
                'active_applications' => Application::where('applicant_id', $applicant->id)
                                                   ->whereIn('status', ['active', 'in_progress'])
                                                   ->count(),
                'successful_placements' => Placement::where('applicant_id', $applicant->id)->count(),
            ],
        ];
    }

    /**
     * Helper method to get database-specific date format
     */
    private function getDateFormat(): string
    {
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}.driver");
        
        return match($connection) {
            'mysql' => 'DATE',
            'pgsql' => 'DATE',
            'sqlite' => 'date',
            default => 'DATE',
        };
    }

    /**
     * Helper method to calculate growth rate
     */
    private function calculateGrowthRate(string $type, string $startDate, string $endDate): float
    {
        $model = match($type) {
            'applicants' => Applicant::class,
            'placements' => Placement::class,
            'companies' => Company::class,
            default => null,
        };

        if (!$model) {
            return 0;
        }

        $currentPeriod = $model::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Calculate previous period
        $daysDiff = Carbon::parse($endDate)->diffInDays(Carbon::parse($startDate));
        $prevStartDate = Carbon::parse($startDate)->subDays($daysDiff)->toDateString();
        $prevEndDate = Carbon::parse($startDate)->subDay()->toDateString();
        
        $previousPeriod = $model::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();

        if ($previousPeriod == 0) {
            return $currentPeriod > 0 ? 100 : 0;
        }

        return round((($currentPeriod - $previousPeriod) / $previousPeriod) * 100, 2);
    }

    /**
     * Calculate average time to placement (PostgreSQL compatible)
     */
    private function calculateAverageTimeToPlacement(): float
    {
        $result = Application::where('status', 'placed')
            ->selectRaw('AVG(EXTRACT(day FROM (updated_at - applied_at))) as avg_days')
            ->value('avg_days');

        return round($result ?? 0, 1);
    }

    /**
     * Calculate application to placement ratio
     */
    private function calculateApplicationToPlacementRatio(): float
    {
        $totalApplications = Application::count();
        $totalPlacements = Placement::count();

        if ($totalApplications == 0) {
            return 0;
        }

        return round(($totalPlacements / $totalApplications) * 100, 2);
    }

    /**
     * Calculate average application processing time (PostgreSQL compatible)
     */
    private function calculateAverageProcessingTime(): float
    {
        $result = Application::whereIn('status', ['accepted', 'rejected'])
            ->whereNotNull('final_decision_at')
            ->selectRaw('AVG(EXTRACT(day FROM (final_decision_at - applied_at))) as avg_days')
            ->value('avg_days');

        return round($result ?? 0, 1);
    }

    /**
     * Get top performing agents
     */
    private function getTopPerformingAgents(): array
    {
        return Agent::join('users', 'agents.user_id', '=', 'users.id')
            ->select('agents.*', DB::raw("CONCAT(users.first_name, ' ', users.last_name) as full_name"))
            ->orderByDesc('agents.total_points')
            ->limit(5)
            ->get()
            ->map(function ($agent) {
                return [
                    'name' => $agent->full_name,
                    'level' => $agent->level ?? 'bronze',
                    'success_rate' => $agent->success_rate ?? 0,
                    'total_commission' => $agent->total_commission ?? 0,
                ];
            })
            ->toArray();
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion(Applicant $applicant): int
    {
        $requiredFields = [
            'nik', 'birth_date', 'birth_place', 'gender', 'address', 'city', 'province',
            'whatsapp_number', 'education_level', 'school_name', 'graduation_year'
        ];

        $completedFields = 0;
        foreach ($requiredFields as $field) {
            if (!empty($applicant->$field)) {
                $completedFields++;
            }
        }

        return round(($completedFields / count($requiredFields)) * 100);
    }
}
