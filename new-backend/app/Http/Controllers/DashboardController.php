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
                $dashboardData['agent_metrics'] = $this->getAgentMetrics($user->agent, $startDate, $endDate);
                break;
                
            case 'applicant':
                $dashboardData['applicant_metrics'] = $this->getApplicantMetrics($user->applicant);
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
            'active_applicants' => Applicant::active()->count(),
            'new_applicants_this_period' => Applicant::whereBetween('created_at', [$startDate, $endDate])->count(),
            
            'total_job_postings' => JobPosting::count(),
            'active_job_postings' => JobPosting::active()->count(),
            'new_jobs_this_period' => JobPosting::whereBetween('created_at', [$startDate, $endDate])->count(),
            
            'total_applications' => Application::count(),
            'active_applications' => Application::active()->count(),
            'new_applications_this_period' => Application::whereBetween('applied_at', [$startDate, $endDate])->count(),
            
            'total_placements' => Placement::count(),
            'active_placements' => Placement::active()->count(),
            'new_placements_this_period' => Placement::whereBetween('start_date', [$startDate, $endDate])->count(),
            
            'total_agents' => Agent::count(),
            'active_agents' => Agent::active()->count(),
            
            'total_companies' => Company::count(),
            'active_companies' => Company::active()->count(),
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
        $data = Applicant::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
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
        return Placement::with('company')
            ->selectRaw('company_id, COUNT(*) as count')
            ->groupBy('company_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'company_name' => $item->company->name ?? 'Unknown',
                    'count' => $item->count,
                ];
            })
            ->values()
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
        return Agent::with('user')
            ->select('id', 'user_id', 'agent_code', 'total_referrals', 'successful_placements', 'success_rate', 'level')
            ->orderByDesc('total_points')
            ->limit(10)
            ->get()
            ->map(function ($agent) {
                return [
                    'name' => $agent->full_name,
                    'agent_code' => $agent->agent_code,
                    'total_referrals' => $agent->total_referrals,
                    'successful_placements' => $agent->successful_placements,
                    'success_rate' => $agent->success_rate,
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
        return JobPosting::with('company')
            ->selectRaw('id, title, company_id, total_applications, total_hired, (total_hired / NULLIF(total_applications, 0) * 100) as success_rate')
            ->whereRaw('total_applications > 0')
            ->orderByDesc('success_rate')
            ->limit(10)
            ->get()
            ->map(function ($job) {
                return [
                    'job_title' => $job->title,
                    'company_name' => $job->company->name ?? 'Unknown',
                    'total_applications' => $job->total_applications,
                    'total_hired' => $job->total_hired,
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
        $recentApplicants = Applicant::with('user', 'agent.user')
            ->orderBy('created_at', 'desc')
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
                        'agent' => $applicant->agent ? $applicant->agent->full_name : null,
                    ],
                    'timestamp' => $applicant->created_at,
                    'icon' => 'user-plus',
                    'color' => 'green',
                ];
            });

        // Recent job postings
        $recentJobs = JobPosting::with('company', 'creator')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($job) {
                return [
                    'type' => 'job_posted',
                    'message' => "New job '{$job->title}' posted by {$job->company->name}",
                    'details' => [
                        'job_title' => $job->title,
                        'company_name' => $job->company->name,
                        'employment_type' => $job->employment_type,
                        'priority' => $job->priority,
                        'created_by' => $job->creator->full_name,
                    ],
                    'timestamp' => $job->created_at,
                    'icon' => 'briefcase',
                    'color' => 'blue',
                ];
            });

        // Recent placements
        $recentPlacements = Placement::with('applicant.user', 'company')
            ->orderBy('start_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($placement) {
                return [
                    'type' => 'placement_created',
                    'message' => "{$placement->applicant->full_name} placed at {$placement->company->name}",
                    'details' => [
                        'applicant_name' => $placement->applicant->full_name,
                        'company_name' => $placement->company->name,
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
        $expiringContracts = Placement::expiring(30)->count();
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
        $urgentJobs = JobPosting::active()->urgent()->count();
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
                                                   ->sum('salary'),
                'total_commission_paid' => Agent::sum('total_commission'),
                'avg_placement_salary' => Placement::whereBetween('start_date', [$startDate, $endDate])
                                                 ->avg('salary'),
            ],
            'growth_metrics' => [
                'applicant_growth_rate' => $this->calculateGrowthRate('applicants', $startDate, $endDate),
                'placement_growth_rate' => $this->calculateGrowthRate('placements', $startDate, $endDate),
                'company_growth_rate' => $this->calculateGrowthRate('companies', $startDate, $endDate),
            ],
            'efficiency_metrics' => [
                'avg_time_to_placement' => $this->calculateAverageTimeToPlacement(),
                'application_to_placement_ratio' => $this->calculateApplicationToPlacementRatio(),
                'top_performing_agents' => Agent::topPerformers(5)->get()->map(function ($agent) {
                    return [
                        'name' => $agent->full_name,
                        'level' => $agent->level,
                        'success_rate' => $agent->success_rate,
                        'total_commission' => $agent->total_commission,
                    ];
                }),
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
                'contracts_expiring_this_month' => Placement::expiring(30)->count(),
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
                'total_referrals' => $agent->total_referrals,
                'successful_placements' => $agent->successful_placements,
                'success_rate' => $agent->success_rate,
                'current_level' => $agent->level,
                'total_points' => $agent->total_points,
                'total_commission' => $agent->total_commission,
            ],
            'period_performance' => [
                'new_referrals' => $agent->applicants()->whereBetween('created_at', [$startDate, $endDate])->count(),
                'new_placements' => $agent->placements()->whereBetween('start_date', [$startDate, $endDate])->count(),
                'commission_earned' => $agent->placements()->whereBetween('start_date', [$startDate, $endDate])->sum('agent_commission'),
            ],
            'recent_referrals' => $agent->applicants()
                                      ->with('user')
                                      ->orderBy('created_at', 'desc')
                                      ->limit(10)
                                      ->get()
                                      ->map(function ($applicant) {
                                          return [
                                              'name' => $applicant->full_name,
                                              'education_level' => $applicant->education_level,
                                              'city' => $applicant->city,
                                              'work_status' => $applicant->work_status,
                                              'registered_at' => $applicant->created_at,
                                          ];
                                      }),
        ];
    }

    /**
     * Get applicant-specific metrics
     */
    private function getApplicantMetrics(Applicant $applicant): array
    {
        return [
            'profile_status' => [
                'profile_completed' => $applicant->isProfileCompleted(),
                'work_status' => $applicant->work_status,
                'profile_completion_percentage' => $this->calculateProfileCompletion($applicant),
            ],
            'application_history' => [
                'total_applications' => $applicant->applications()->count(),
                'active_applications' => $applicant->applications()->active()->count(),
                'successful_placements' => $applicant->placements()->count(),
            ],
            'recent_applications' => $applicant->applications()
                                            ->with('jobPosting.company')
                                            ->orderBy('applied_at', 'desc')
                                            ->limit(10)
                                            ->get()
                                            ->map(function ($application) {
                                                return [
                                                    'job_title' => $application->jobPosting->title,
                                                    'company_name' => $application->jobPosting->company->name,
                                                    'current_stage' => $application->current_stage,
                                                    'status' => $application->status,
                                                    'applied_at' => $application->applied_at,
                                                ];
                                            }),
            'matching_jobs' => JobPosting::active()
                                        ->limit(5)
                                        ->get()
                                        ->filter(function ($job) use ($applicant) {
                                            $match = $applicant->matchesJobRequirements($job);
                                            return $match['score'] >= 60; // Only jobs with 60%+ match
                                        })
                                        ->map(function ($job) use ($applicant) {
                                            $match = $applicant->matchesJobRequirements($job);
                                            return [
                                                'id' => $job->id,
                                                'title' => $job->title,
                                                'company_name' => $job->company->name,
                                                'work_city' => $job->work_city,
                                                'salary_range' => $job->salary_range,
                                                'matching_score' => $match['score'],
                                                'application_deadline' => $job->application_deadline,
                                            ];
                                        })
                                        ->values(),
        ];
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
     * Calculate average time to placement
     */
    private function calculateAverageTimeToPlacement(): float
    {
        $avgDays = Application::where('status', 'placed')
            ->selectRaw('AVG(DATEDIFF(updated_at, applied_at)) as avg_days')
            ->value('avg_days');

        return round($avgDays ?? 0, 1);
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
     * Calculate average application processing time
     */
    private function calculateAverageProcessingTime(): float
    {
        $avgDays = Application::whereIn('status', ['accepted', 'rejected'])
            ->selectRaw('AVG(DATEDIFF(final_decision_at, applied_at)) as avg_days')
            ->value('avg_days');

        return round($avgDays ?? 0, 1);
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion(Applicant $applicant): int
    {
        $requiredFields = [
            'nik', 'birth_date', 'birth_place', 'gender', 'address', 'city', 'province',
            'whatsapp_number', 'education_level', 'school_name', 'graduation_year',
            'work_experience', 'skills', 'preferred_positions', 'ktp_file', 'cv_file'
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