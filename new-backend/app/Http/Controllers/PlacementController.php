<?php

namespace App\Http\Controllers;

use App\Models\Placement;
use App\Models\Application;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PlacementController extends Controller
{
    /**
     * Display a listing of placements
     */
    public function index(Request $request): JsonResponse
    {
        $query = Placement::with([
            'applicant.user',
            'jobPosting.company',
            'company',
            'agent.user',
            'placedBy'
        ]);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        if ($request->has('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        if ($request->has('start_date_from')) {
            $query->where('start_date', '>=', $request->start_date_from);
        }

        if ($request->has('start_date_to')) {
            $query->where('start_date', '<=', $request->start_date_to);
        }

        if ($request->has('expiring_within_days')) {
            $days = (int) $request->expiring_within_days;
            $query->expiring($days);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'start_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $placements = $query->paginate($perPage);

        // Add computed fields
        $placements->getCollection()->transform(function ($placement) {
            $placement->days_until_expiry = $placement->days_until_expiry;
            $placement->completion_percentage = $placement->completion_percentage;
            return $placement;
        });

        return response()->json([
            'success' => true,
            'data' => $placements,
        ]);
    }

    /**
     * Store a newly created placement
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:applications,id',
            'employee_id' => 'nullable|string|max:255',
            'position_title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'work_location' => 'required|string|max:255',
            'contract_type' => 'required|in:magang,pkwt,project',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'contract_duration_months' => 'nullable|integer|min:1|max:60',
            'salary' => 'required|numeric|min:0',
            'benefits' => 'nullable|array',
            'contract_terms' => 'nullable|array',
            'is_renewable' => 'boolean',
            'placement_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Get application and validate
            $application = Application::with(['applicant', 'jobPosting.company', 'agent'])
                ->find($request->application_id);

            if ($application->status !== 'accepted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only accepted applications can be placed',
                ], 422);
            }

            // Check if placement already exists
            $existingPlacement = Placement::where('application_id', $request->application_id)->first();
            if ($existingPlacement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Placement already exists for this application',
                ], 422);
            }

            // Create placement
            $placementData = $request->all();
            $placementData['placement_number'] = Placement::generatePlacementNumber();
            $placementData['applicant_id'] = $application->applicant_id;
            $placementData['job_posting_id'] = $application->job_posting_id;
            $placementData['company_id'] = $application->jobPosting->company_id;
            $placementData['agent_id'] = $application->agent_id;
            $placementData['placed_by'] = Auth::id();
            $placementData['status'] = 'pending_start';

            $placement = Placement::create($placementData);

            // Update application status
            $application->update(['status' => 'placed']);

            // Update applicant work status
            $application->applicant->update(['work_status' => 'working']);

            // Update job posting hired count
            $application->jobPosting->increment('total_hired');

            // Process agent commission if applicable
            if ($placement->agent) {
                $placement->processAgentCommission();
            }

            // Send placement confirmation WhatsApp
            $this->sendPlacementConfirmation($placement);

            $placement->load([
                'applicant.user',
                'jobPosting.company',
                'company',
                'agent.user',
                'placedBy'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Placement created successfully',
                'data' => $placement,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create placement: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified placement
     */
    public function show(Placement $placement): JsonResponse
    {
        $placement->load([
            'application',
            'applicant.user',
            'jobPosting.company',
            'company',
            'agent.user',
            'placedBy',
            'terminatedBy'
        ]);

        // Add computed data
        $data = $placement->toArray();
        $data['days_until_expiry'] = $placement->days_until_expiry;
        $data['completion_percentage'] = $placement->completion_percentage;
        $data['contract_duration_days'] = $placement->contract_duration_days;
        $data['days_completed'] = $placement->days_completed;
        $data['is_expiring_soon'] = $placement->isExpiringSoon();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Update the specified placement
     */
    public function update(Request $request, Placement $placement): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'sometimes|nullable|string|max:255',
            'position_title' => 'sometimes|required|string|max:255',
            'department' => 'sometimes|required|string|max:255',
            'work_location' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|nullable|date|after:start_date',
            'salary' => 'sometimes|required|numeric|min:0',
            'benefits' => 'sometimes|nullable|array',
            'contract_terms' => 'sometimes|nullable|array',
            'status' => 'sometimes|required|in:pending_start,active,completed,terminated,expired,on_hold',
            'performance_score' => 'sometimes|nullable|numeric|min:0|max:100',
            'attendance_rate' => 'sometimes|nullable|integer|min:0|max:100',
            'supervisor_feedback' => 'sometimes|nullable|string',
            'is_renewable' => 'sometimes|boolean',
            'renewal_offered' => 'sometimes|boolean',
            'renewal_accepted' => 'sometimes|boolean',
            'renewal_decision_deadline' => 'sometimes|nullable|date',
            'placement_notes' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $placement->update($request->all());
            $placement->load([
                'applicant.user',
                'jobPosting.company',
                'company',
                'agent.user'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Placement updated successfully',
                'data' => $placement,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update placement: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified placement
     */
    public function destroy(Placement $placement): JsonResponse
    {
        try {
            // Check if placement can be deleted
            if (in_array($placement->status, ['active', 'completed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete active or completed placements',
                ], 422);
            }

            $placement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Placement deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete placement: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Terminate placement
     */
    public function terminate(Request $request, Placement $placement): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'termination_reason' => 'required|in:contract_completion,mutual_agreement,company_decision,employee_resignation,performance_issues,misconduct,force_majeure,other',
            'termination_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if ($placement->status === 'terminated') {
                return response()->json([
                    'success' => false,
                    'message' => 'Placement is already terminated',
                ], 422);
            }

            $success = $placement->terminate(
                $request->termination_reason,
                $request->termination_notes,
                Auth::id()
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Placement terminated successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to terminate placement',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to terminate placement: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Complete placement
     */
    public function complete(Placement $placement): JsonResponse
    {
        try {
            if ($placement->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Placement is already completed',
                ], 422);
            }

            $success = $placement->complete();

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Placement completed successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete placement',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete placement: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add performance review
     */
    public function addPerformanceReview(Request $request, Placement $placement): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'review_period' => 'required|string|max:50',
            'score' => 'required|numeric|min:0|max:100',
            'attendance' => 'nullable|integer|min:0|max:100',
            'punctuality' => 'nullable|integer|min:0|max:100',
            'work_quality' => 'nullable|integer|min:0|max:100',
            'teamwork' => 'nullable|integer|min:0|max:100',
            'communication' => 'nullable|integer|min:0|max:100',
            'feedback' => 'nullable|string',
            'strengths' => 'nullable|array',
            'areas_for_improvement' => 'nullable|array',
            'goals_next_period' => 'nullable|array',
            'reviewer_name' => 'required|string|max:255',
            'reviewer_position' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $reviewData = $request->all();
            $reviewData['reviewed_by'] = Auth::id();
            $reviewData['review_date'] = now()->toDateString();

            $placement->addPerformanceReview($reviewData);

            return response()->json([
                'success' => true,
                'message' => 'Performance review added successfully',
                'data' => [
                    'performance_score' => $placement->performance_score,
                    'total_reviews' => count($placement->performance_reviews ?? []),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add performance review: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get expiring placements
     */
    public function expiring(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        
        $placements = Placement::with([
            'applicant.user',
            'company',
            'agent.user'
        ])
        ->expiring($days)
        ->orderBy('end_date', 'asc')
        ->get();

        // Add computed fields
        $placements->transform(function ($placement) {
            $placement->days_until_expiry = $placement->days_until_expiry;
            return $placement;
        });

        return response()->json([
            'success' => true,
            'data' => $placements,
            'meta' => [
                'total_expiring' => $placements->count(),
                'days_filter' => $days,
            ],
        ]);
    }

    /**
     * Get placement statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_placements' => Placement::count(),
            'active_placements' => Placement::active()->count(),
            'completed_placements' => Placement::where('status', 'completed')->count(),
            'terminated_placements' => Placement::where('status', 'terminated')->count(),
            'new_this_month' => Placement::whereMonth('start_date', now()->month)->count(),
            'expiring_30_days' => Placement::expiring(30)->count(),
            'expiring_7_days' => Placement::expiring(7)->count(),
            
            'by_status' => Placement::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
                
            'by_contract_type' => Placement::selectRaw('contract_type, COUNT(*) as count')
                ->groupBy('contract_type')
                ->pluck('count', 'contract_type'),
                
            'by_company' => Placement::with('company')
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
                }),
                
            'performance_metrics' => [
                'avg_performance_score' => Placement::whereNotNull('performance_score')->avg('performance_score'),
                'avg_attendance_rate' => Placement::whereNotNull('attendance_rate')->avg('attendance_rate'),
                'high_performers' => Placement::where('performance_score', '>=', 80)->count(),
                'low_performers' => Placement::where('performance_score', '<', 60)->count(),
            ],
            
            'financial_metrics' => [
                'total_salary_value' => Placement::sum('salary'),
                'avg_salary' => Placement::avg('salary'),
                'total_commission_paid' => Placement::sum('agent_commission'),
                'commission_pending' => Placement::where('commission_paid', false)->sum('agent_commission'),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Send placement confirmation WhatsApp message
     */
    private function sendPlacementConfirmation(Placement $placement): void
    {
        $message = "🎉 *SELAMAT! ANDA DITERIMA BEKERJA* 🎉\n\n";
        $message .= "Halo {$placement->applicant->full_name}!\n\n";
        $message .= "Kami dengan bangga menginformasikan bahwa Anda telah *DITERIMA* untuk bekerja!\n\n";
        $message .= "📋 *Detail Penempatan:*\n";
        $message .= "🏢 Perusahaan: {$placement->company->name}\n";
        $message .= "💼 Posisi: {$placement->position_title}\n";
        $message .= "🏛️ Departemen: {$placement->department}\n";
        $message .= "📍 Lokasi: {$placement->work_location}\n";
        $message .= "💰 Gaji: Rp " . number_format($placement->salary, 0, ',', '.') . "\n";
        $message .= "📅 Mulai Kerja: " . $placement->start_date->format('d M Y') . "\n";
        $message .= "📞 Placement No: {$placement->placement_number}\n\n";
        
        if ($placement->employee_id) {
            $message .= "🆔 Employee ID: {$placement->employee_id}\n\n";
        }
        
        $message .= "Tim HR akan segera menghubungi Anda untuk:\n";
        $message .= "• Penandatanganan kontrak kerja\n";
        $message .= "• Proses onboarding\n";
        $message .= "• Informasi hari pertama kerja\n\n";
        $message .= "Pastikan nomor telepon Anda selalu aktif.\n\n";
        $message .= "Selamat bergabung! 🚀\n\n";
        $message .= "_Pesan otomatis dari sistem recruitment_";

        \App\Models\WhatsAppLog::create([
            'session_id' => 'main_session',
            'phone_number' => $placement->applicant->whatsapp_number,
            'message_type' => 'template',
            'message_content' => $message,
            'template_name' => 'placement_confirmation',
            'template_variables' => [
                'applicant_name' => $placement->applicant->full_name,
                'company_name' => $placement->company->name,
                'position_title' => $placement->position_title,
                'start_date' => $placement->start_date->format('d M Y'),
                'placement_number' => $placement->placement_number,
            ],
            'status' => 'pending',
            'context_type' => 'placement_confirmation',
            'context_id' => $placement->id,
            'applicant_id' => $placement->applicant_id,
        ]);
    }
}