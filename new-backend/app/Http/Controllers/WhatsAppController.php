<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class WhatsAppController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Get WhatsApp gateway status and session information
     */
    public function status(): JsonResponse
    {
        try {
            $status = $this->whatsAppService->checkGatewayStatus();
            
            return response()->json([
                'success' => true,
                'data' => $status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check WhatsApp status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Start WhatsApp session
     */
    public function startSession(): JsonResponse
    {
        try {
            $result = $this->whatsAppService->startSession();
            
            return response()->json([
                'success' => $result['status'] === 'success',
                'message' => $result['message'],
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start WhatsApp session: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Stop WhatsApp session
     */
    public function stopSession(): JsonResponse
    {
        try {
            $result = $this->whatsAppService->stopSession();
            
            return response()->json([
                'success' => $result['status'] === 'success',
                'message' => $result['message'],
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to stop WhatsApp session: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send test message
     */
    public function sendTestMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:10|max:15',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // First check if session is active
            $status = $this->whatsAppService->checkGatewayStatus();
            
            if ($status['status'] !== 'connected') {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp session is not active. Please start the session first.',
                    'session_status' => $status,
                ], 422);
            }

            // Create a test applicant object for the method
            $testApplicant = new \stdClass();
            $testApplicant->phone = $request->phone;
            
            $success = $this->whatsAppService->sendMessage(
                $request->phone, 
                $request->message, 
                'test_message'
            );
            
            return response()->json([
                'success' => $success,
                'message' => $success ? 'Test message sent successfully' : 'Failed to send test message',
                'data' => [
                    'phone' => $request->phone,
                    'message' => $request->message,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test message: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send test image
     */
    public function sendTestImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:10|max:15',
            'image_url' => 'required|url',
            'caption' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $success = $this->whatsAppService->sendImage(
                $request->phone,
                $request->image_url,
                $request->caption ?? ''
            );
            
            return response()->json([
                'success' => $success,
                'message' => $success ? 'Test image sent successfully' : 'Failed to send test image',
                'data' => [
                    'phone' => $request->phone,
                    'image_url' => $request->image_url,
                    'caption' => $request->caption,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test image: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send test document
     */
    public function sendTestDocument(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:10|max:15',
            'document_url' => 'required|url',
            'document_name' => 'required|string|max:255',
            'caption' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $success = $this->whatsAppService->sendDocument(
                $request->phone,
                $request->document_url,
                $request->document_name,
                $request->caption ?? ''
            );
            
            return response()->json([
                'success' => $success,
                'message' => $success ? 'Test document sent successfully' : 'Failed to send test document',
                'data' => [
                    'phone' => $request->phone,
                    'document_url' => $request->document_url,
                    'document_name' => $request->document_name,
                    'caption' => $request->caption,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test document: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get message statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : null;
            $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : null;
            
            $stats = $this->whatsAppService->getMessageStats($startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get message statistics: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get WhatsApp logs
     */
    public function logs(Request $request): JsonResponse
    {
        try {
            $query = \App\Models\WhatsAppLog::query();

            // Apply filters
            if ($request->has('phone')) {
                $query->where('phone_number', 'like', '%' . $request->phone . '%');
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('message_type')) {
                $query->where('message_type', $request->message_type);
            }

            if ($request->has('date_from')) {
                $query->where('sent_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('sent_at', '<=', $request->date_to);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'sent_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 20);
            $logs = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $logs,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get WhatsApp logs: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test complete WhatsApp workflow
     */
    public function testWorkflow(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'test_phone' => 'required|string|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $testPhone = $request->test_phone;
            $workflow = [];

            // Step 1: Check gateway status
            $status = $this->whatsAppService->checkGatewayStatus();
            $workflow['step_1_gateway_status'] = $status;

            if ($status['status'] !== 'connected') {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp session is not connected. Please ensure the session is active.',
                    'workflow' => $workflow,
                ], 422);
            }

            // Step 2: Test welcome message
            $welcomeMessage = "🎉 *TEST WELCOME MESSAGE* 🎉\n\nHalo! Ini adalah test message dari sistem Job Placement.\n\nSistem WhatsApp integration berfungsi dengan baik! ✅";
            $welcomeSuccess = $this->whatsAppService->sendMessage($testPhone, $welcomeMessage, 'test_welcome');
            $workflow['step_2_welcome_message'] = [
                'success' => $welcomeSuccess,
                'message' => $welcomeMessage
            ];

            // Step 3: Test job broadcast message
            sleep(2); // Prevent rate limiting
            $jobMessage = "🚀 *TEST JOB BROADCAST* 🚀\n\n🏢 Perusahaan: PT Test Company\n💼 Posisi: Software Developer\n📍 Lokasi: Jakarta\n⏰ Deadline: 30 Juni 2025\n\nIni adalah test broadcast lowongan kerja! 📢";
            $jobSuccess = $this->whatsAppService->sendMessage($testPhone, $jobMessage, 'test_job_broadcast');
            $workflow['step_3_job_broadcast'] = [
                'success' => $jobSuccess,
                'message' => $jobMessage
            ];

            // Step 4: Test image sending (if test image URL provided)
            if ($request->has('test_image_url')) {
                sleep(2);
                $imageSuccess = $this->whatsAppService->sendImage(
                    $testPhone, 
                    $request->test_image_url, 
                    'Test image dari sistem Job Placement'
                );
                $workflow['step_4_image_test'] = [
                    'success' => $imageSuccess,
                    'image_url' => $request->test_image_url
                ];
            }

            // Step 5: Get message statistics
            $stats = $this->whatsAppService->getMessageStats();
            $workflow['step_5_statistics'] = $stats;

            $overallSuccess = $welcomeSuccess && $jobSuccess;

            return response()->json([
                'success' => $overallSuccess,
                'message' => $overallSuccess ? 'WhatsApp workflow test completed successfully!' : 'Some tests failed',
                'data' => [
                    'test_phone' => $testPhone,
                    'workflow_steps' => $workflow,
                    'summary' => [
                        'gateway_status' => $status['status'],
                        'messages_sent' => ($welcomeSuccess ? 1 : 0) + ($jobSuccess ? 1 : 0),
                        'total_tests' => 2,
                        'success_rate' => $overallSuccess ? '100%' : '50%'
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'WhatsApp workflow test failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
