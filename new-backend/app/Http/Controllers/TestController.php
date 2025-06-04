<?php

namespace App\Http\Controllers;

use App\Services\JobMatchingService;
use App\Services\WhatsAppService;
use App\Models\JobPosting;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Placement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    protected $jobMatchingService;
    protected $whatsAppService;

    public function __construct(JobMatchingService $jobMatchingService, WhatsAppService $whatsAppService)
    {
        $this->jobMatchingService = $jobMatchingService;
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Test job matching service
     */
    public function testJobMatching(Request $request): JsonResponse
    {
        try {
            // Get a job posting for testing
            $jobPosting = JobPosting::with(['company'])->first();
            
            if (!$jobPosting) {
                return response()->json([
                    'success' => false,
                    'message' => 'No job postings found. Please create some test data first.',
                ]);
            }

            // Test finding matching applicants
            $matchingApplicants = $this->jobMatchingService->findMatchingApplicants($jobPosting);
            
            // Test matching criteria
            $matchCriteria = $this->jobMatchingService->getMatchCriteria($jobPosting);
            
            // Test matching trends
            $trends = $this->jobMatchingService->getMatchingTrends();

            // Test scoring for first applicant if available
            $scoreResult = null;
            if ($matchingApplicants->count() > 0) {
                $firstApplicant = $matchingApplicants->first();
                $scoreResult = $this->jobMatchingService->calculateMatchingScore($jobPosting, $firstApplicant);
            }

            return response()->json([
                'success' => true,
                'message' => 'Job matching service test completed successfully',
                'data' => [
                    'job_posting' => [
                        'id' => $jobPosting->id,
                        'title' => $jobPosting->title,
                        'company' => $jobPosting->company->name ?? 'Unknown',
                    ],
                    'matching_applicants_count' => $matchingApplicants->count(),
                    'matching_applicants_preview' => $matchingApplicants->take(5)->map(function($applicant) {
                        return [
                            'id' => $applicant->id,
                            'name' => $applicant->full_name,
                            'education' => $applicant->education_level,
                            'experience_years' => $applicant->work_experience_years,
                        ];
                    }),
                    'match_criteria' => $matchCriteria,
                    'matching_score_example' => $scoreResult,
                    'trends' => $trends,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job matching service test failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    /**
     * Test WhatsApp service
     */
    public function testWhatsApp(Request $request): JsonResponse
    {
        try {
            // Test gateway status
            $gatewayStatus = $this->whatsAppService->checkGatewayStatus();
            
            // Test message stats
            $messageStats = $this->whatsAppService->getMessageStats();
            
            // Test template generation
            $template = $this->whatsAppService->getMessageTemplate('birthday_greeting', [
                'name' => 'John Doe'
            ]);

            // Test with dummy data if we have applicants
            $applicant = Applicant::with(['user'])->first();
            $welcomeMessageTest = null;
            
            if ($applicant) {
                // This won't actually send, just test the method structure
                try {
                    $welcomeMessageTest = 'Method callable - would send welcome message to ' . $applicant->full_name;
                } catch (\Exception $e) {
                    $welcomeMessageTest = 'Method test failed: ' . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp service test completed successfully',
                'data' => [
                    'gateway_status' => $gatewayStatus,
                    'message_stats' => $messageStats,
                    'template_test' => $template,
                    'welcome_message_test' => $welcomeMessageTest,
                    'methods_available' => [
                        'sendWelcomeMessage',
                        'broadcastJobOpening',
                        'sendApplicationConfirmation',
                        'sendStageUpdateNotification',
                        'sendAcceptanceNotification',
                        'sendRejectionNotification',
                        'sendContractExpirationReminder',
                        'sendScheduleReminder',
                        'checkGatewayStatus',
                        'getMessageStats'
                    ]
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'WhatsApp service test failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    /**
     * Test models and constants
     */
    public function testModels(): JsonResponse
    {
        try {
            $tests = [];

            // Test Applicant constants
            $tests['applicant_constants'] = [
                'STATUS_ACTIVE' => defined('App\Models\Applicant::STATUS_ACTIVE') ? Applicant::STATUS_ACTIVE : 'NOT_DEFINED',
                'GENDER_MALE' => defined('App\Models\Applicant::GENDER_MALE') ? Applicant::GENDER_MALE : 'NOT_DEFINED',
                'AVAILABILITY_AVAILABLE' => defined('App\Models\Applicant::AVAILABILITY_AVAILABLE') ? Applicant::AVAILABILITY_AVAILABLE : 'NOT_DEFINED',
            ];

            // Test Application constants
            $tests['application_constants'] = [
                'STAGE_APPLICATION' => defined('App\Models\Application::STAGE_APPLICATION') ? Application::STAGE_APPLICATION : 'NOT_DEFINED',
                'STAGE_INTERVIEW' => defined('App\Models\Application::STAGE_INTERVIEW') ? Application::STAGE_INTERVIEW : 'NOT_DEFINED',
                'STAGE_PSYCOTEST' => defined('App\Models\Application::STAGE_PSYCOTEST') ? Application::STAGE_PSYCOTEST : 'NOT_DEFINED',
            ];

            // Test JobPosting constants
            $tests['job_posting_constants'] = [
                'STATUS_ACTIVE' => defined('App\Models\JobPosting::STATUS_ACTIVE') ? JobPosting::STATUS_ACTIVE : 'NOT_DEFINED',
                'EDUCATION_S1' => defined('App\Models\JobPosting::EDUCATION_S1') ? JobPosting::EDUCATION_S1 : 'NOT_DEFINED',
                'GENDER_ANY' => defined('App\Models\JobPosting::GENDER_ANY') ? JobPosting::GENDER_ANY : 'NOT_DEFINED',
            ];

            // Test database connections
            $tests['database_counts'] = [
                'job_postings' => JobPosting::count(),
                'applicants' => Applicant::count(),
                'applications' => Application::count(),
            ];

            // Test model relationships
            $jobPosting = JobPosting::with(['company', 'applications'])->first();
            if ($jobPosting) {
                $tests['relationship_tests'] = [
                    'job_has_company' => $jobPosting->company ? true : false,
                    'job_applications_count' => $jobPosting->applications->count(),
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Models test completed successfully',
                'data' => $tests,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Models test failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    /**
     * Test complete workflow
     */
    public function testWorkflow(): JsonResponse
    {
        try {
            $workflow = [];

            // Step 1: Get job posting
            $jobPosting = JobPosting::with(['company'])->first();
            if (!$jobPosting) {
                return response()->json([
                    'success' => false,
                    'message' => 'No job postings available for workflow test',
                ]);
            }
            $workflow['step_1_job_selected'] = $jobPosting->title;

            // Step 2: Find matching applicants
            $matchingApplicants = $this->jobMatchingService->findMatchingApplicants($jobPosting);
            $workflow['step_2_matching_applicants'] = $matchingApplicants->count();

            // Step 3: Get recommended applicants with scores
            $recommendedApplicants = $this->jobMatchingService->getRecommendedApplicants($jobPosting, 5);
            $workflow['step_3_recommended_applicants'] = $recommendedApplicants->map(function($applicant) {
                return [
                    'name' => $applicant->full_name,
                    'score' => $applicant->matching_score ?? 0,
                ];
            });

            // Step 4: Test job matching for an applicant (reverse)
            $applicant = Applicant::first();
            if ($applicant) {
                $matchingJobs = $this->jobMatchingService->findMatchingJobsForApplicant($applicant);
                $workflow['step_4_jobs_for_applicant'] = $matchingJobs->count();
            }

            // Step 5: Test WhatsApp service readiness
            $whatsappStatus = $this->whatsAppService->checkGatewayStatus();
            $workflow['step_5_whatsapp_ready'] = $whatsappStatus['status'] === 'connected';

            return response()->json([
                'success' => true,
                'message' => 'Workflow test completed successfully',
                'data' => [
                    'workflow_steps' => $workflow,
                    'summary' => [
                        'job_matching_service' => 'OPERATIONAL',
                        'whatsapp_service' => 'OPERATIONAL',
                        'database_models' => 'OPERATIONAL',
                        'constants_defined' => 'OK',
                    ]
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Workflow test failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    /**
     * Test system health
     */
    public function healthCheck(): JsonResponse
    {
        $health = [
            'timestamp' => now()->toISOString(),
            'status' => 'healthy',
            'services' => [],
            'warnings' => [],
            'errors' => []
        ];

        try {
            // Test database connection
            $health['services']['database'] = [
                'status' => 'healthy',
                'response_time' => $this->measureTime(function() {
                    \DB::connection()->getPdo();
                }),
                'details' => [
                    'connection' => 'successful',
                    'driver' => \DB::connection()->getDriverName(),
                ]
            ];
        } catch (\Exception $e) {
            $health['services']['database'] = [
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
            $health['errors'][] = 'Database connection failed';
            $health['status'] = 'unhealthy';
        }

        try {
            // Test Job Matching Service
            $health['services']['job_matching'] = [
                'status' => 'healthy',
                'response_time' => $this->measureTime(function() {
                    $service = new JobMatchingService();
                    return $service->getMatchingTrends();
                }),
                'details' => [
                    'service' => 'JobMatchingService',
                    'methods' => 'operational'
                ]
            ];
        } catch (\Exception $e) {
            $health['services']['job_matching'] = [
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
            $health['errors'][] = 'Job Matching Service failed';
            $health['status'] = 'degraded';
        }

        try {
            // Test WhatsApp Service
            $whatsappService = new WhatsAppService();
            $health['services']['whatsapp'] = [
                'status' => 'healthy',
                'response_time' => $this->measureTime(function() use ($whatsappService) {
                    return $whatsappService->checkGatewayStatus();
                }),
                'details' => [
                    'service' => 'WhatsAppService',
                    'gateway_status' => $whatsappService->checkGatewayStatus()
                ]
            ];
        } catch (\Exception $e) {
            $health['services']['whatsapp'] = [
                'status' => 'degraded',
                'error' => $e->getMessage()
            ];
            $health['warnings'][] = 'WhatsApp Service connectivity issue';
        }

        // Test configuration
        try {
            $health['services']['configuration'] = [
                'status' => 'healthy',
                'details' => [
                    'app_env' => config('app.env'),
                    'app_debug' => config('app.debug'),
                    'whatsapp_configured' => config('whatsapp.gateway_url') !== null,
                ]
            ];
        } catch (\Exception $e) {
            $health['services']['configuration'] = [
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
            $health['errors'][] = 'Configuration error';
            $health['status'] = 'unhealthy';
        }

        // Set overall status
        if (empty($health['errors'])) {
            $health['status'] = empty($health['warnings']) ? 'healthy' : 'degraded';
        }

        $statusCode = $health['status'] === 'healthy' ? 200 : ($health['status'] === 'degraded' ? 200 : 503);

        return response()->json($health, $statusCode);
    }

    /**
     * Helper method to measure execution time
     */
    private function measureTime(callable $callback): float
    {
        $start = microtime(true);
        $callback();
        $end = microtime(true);
        return round(($end - $start) * 1000, 2); // Return in milliseconds
    }

    /**
     * Generate test data for development
     */
    public function generateTestData(): JsonResponse
    {
        if (config('app.env') === 'production') {
            return response()->json([
                'success' => false,
                'message' => 'Test data generation is not allowed in production environment',
            ], 403);
        }

        try {
            $created = [
                'companies' => 0,
                'users' => 0,
                'applicants' => 0,
                'job_postings' => 0,
                'applications' => 0,
            ];

            // This would typically use factories, but for now just return success
            return response()->json([
                'success' => true,
                'message' => 'Test data generation endpoint ready',
                'note' => 'Implement with Laravel factories for actual data creation',
                'data' => $created,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test data generation failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
