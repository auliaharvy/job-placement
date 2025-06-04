<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Applicant;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Display a listing of applications
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Application::with([
            'applicant.user', 
            'jobPosting.company', 
            'agent.user',
            'screener',
            'interviewer'
        ]);

        // Role-based filtering
        if ($user->isApplicant()) {
            $query->where('applicant_id', $user->applicant->id);
        } elseif ($user->isAgent()) {
            $query->where('agent_id', $user->agent->id);
        }

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('stage')) {
            $query->where('current_stage', $request->stage);
        }

        if ($request->has('job_id')) {
            $query->where('job_posting_id', $request->job_id);
        }

        if ($request->has('applicant_id')) {
            $query->where('applicant_id', $request->applicant_id);
        }

        if ($request->has('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        if ($request->has('date_from')) {
            $query->where('applied_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('applied_at', '<=', $request->date_to);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'applied_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $applications = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $applications,
        ]);
    }

    /**
     * Store a newly created application
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'applicant_id' => 'required|exists:applicants,id',
            'job_posting_id' => 'required|exists:job_postings,id',
            'agent_id' => 'nullable|exists:agents,id',
            'source' => 'required|in:direct,agent_referral,whatsapp_broadcast,walk_in',
            'applicant_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Check if already applied
            $existingApplication = Application::where('applicant_id', $request->applicant_id)
                ->where('job_posting_id', $request->job_posting_id)
                ->first();

            if ($existingApplication) {
                return response()->json([
                    'success' => false,
                    'message' => 'Applicant has already applied to this job',
                ], 422);
            }

            // Check if job is still accepting applications
            $jobPosting = JobPosting::find($request->job_posting_id);
            if (!$jobPosting->isAcceptingApplications()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This job is no longer accepting applications',
                ], 422);
            }

            // Get applicant for matching
            $applicant = Applicant::find($request->applicant_id);
            $matchResult = $applicant->matchesJobRequirements($jobPosting);

            // Create application
            $application = Application::create([
                'applicant_id' => $request->applicant_id,
                'job_posting_id' => $request->job_posting_id,
                'agent_id' => $request->agent_id,
                'application_number' => Application::generateApplicationNumber(),
                'source' => $request->source,
                'applied_at' => now(),
                'current_stage' => 'applied',
                'status' => 'active',
                'matching_score' => $matchResult['score'],
                'matching_details' => $matchResult['details'],
                'applicant_notes' => $request->applicant_notes,
            ]);

            // Update job posting application count
            $jobPosting->increment('total_applications');

            // Update agent referral count if applicable
            if ($request->agent_id) {
                $agent = \App\Models\Agent::find($request->agent_id);
                $agent->addPoints(25, "Application submitted for job #{$jobPosting->id}");
            }

            // Send application confirmation via WhatsApp
            $application->sendStageProgressionNotification('applied');

            $application->load(['applicant.user', 'jobPosting.company', 'agent.user']);

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'data' => $application,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified application
     */
    public function show(Application $application): JsonResponse
    {
        $application->load([
            'applicant.user',
            'jobPosting.company',
            'agent.user',
            'screener',
            'interviewer',
            'finalDecisionMaker'
        ]);

        // Add computed data
        $data = $application->toArray();
        $data['days_since_application'] = $application->days_since_application;
        $data['overall_score'] = $application->overall_score;
        $data['can_proceed_to_next_stage'] = $application->canProceedToNextStage();
        $data['next_stage'] = $application->getNextStage();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Update the specified application
     */
    public function update(Request $request, Application $application): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_stage' => 'sometimes|required|in:applied,screening,psikotes,interview,medical,final_review,accepted,rejected',
            'status' => 'sometimes|required|in:active,withdrawn,rejected,accepted,placed',
            'internal_notes' => 'sometimes|nullable|string',
            'documents_verified' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $application->update($request->all());
            $application->load(['applicant.user', 'jobPosting.company', 'agent.user']);

            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully',
                'data' => $application,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update application: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified application
     */
    public function destroy(Application $application): JsonResponse
    {
        try {
            // Check if application can be deleted
            if ($application->status === 'placed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete application that has been placed',
                ], 422);
            }

            $application->delete();

            // Update job posting application count
            $application->jobPosting->decrement('total_applications');

            return response()->json([
                'success' => true,
                'message' => 'Application deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete application: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Progress application to next stage
     */
    public function progressStage(Request $request, Application $application): JsonResponse
    {
        if (!$application->canProceedToNextStage()) {
            return response()->json([
                'success' => false,
                'message' => 'Application cannot proceed to next stage. Complete current stage first.',
            ], 422);
        }

        try {
            $success = $application->progressToNextStage();
            
            if ($success) {
                $application->refresh();
                $application->load(['applicant.user', 'jobPosting.company']);

                return response()->json([
                    'success' => true,
                    'message' => 'Application progressed to next stage successfully',
                    'data' => [
                        'current_stage' => $application->current_stage,
                        'next_stage' => $application->getNextStage(),
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to progress application to next stage',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to progress application: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject application
     */
    public function reject(Request $request, Application $application): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $success = $application->reject($request->reason, Auth::id());
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Application rejected successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject application',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject application: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Accept application
     */
    public function accept(Request $request, Application $application): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $success = $application->accept(Auth::id(), $request->notes);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Application accepted successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to accept application',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept application: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Schedule interview
     */
    public function scheduleInterview(Request $request, Application $application): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'interview_scheduled_at' => 'required|date|after:now',
            'interview_location' => 'required|string|max:255',
            'interview_type' => 'required|in:online,offline',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $application->update([
                'interview_scheduled_at' => $request->interview_scheduled_at,
                'interview_location' => $request->interview_location,
                'interview_type' => $request->interview_type,
                'interview_notes' => $request->notes,
                'interviewed_by' => Auth::id(),
            ]);

            // Send WhatsApp notification
            $message = "📅 *JADWAL INTERVIEW*\n\n";
            $message .= "Halo {$application->applicant->full_name}!\n\n";
            $message .= "Selamat! Anda telah lolos ke tahap interview untuk posisi *{$application->jobPosting->title}* di {$application->jobPosting->company->name}.\n\n";
            $message .= "📋 *Detail Interview:*\n";
            $message .= "🗓️ Tanggal: " . \Carbon\Carbon::parse($request->interview_scheduled_at)->format('d M Y H:i') . "\n";
            $message .= "📍 Lokasi: {$request->interview_location}\n";
            $message .= "💻 Tipe: " . ucfirst($request->interview_type) . "\n\n";
            $message .= "Harap datang tepat waktu dan persiapkan diri dengan baik.\n\n";
            $message .= "Semoga sukses! 🍀";

            \App\Models\WhatsAppLog::create([
                'session_id' => 'main_session',
                'phone_number' => $application->applicant->whatsapp_number,
                'message_type' => 'template',
                'message_content' => $message,
                'template_name' => 'interview_scheduled',
                'status' => 'pending',
                'context_type' => 'interview_scheduled',
                'context_id' => $application->id,
                'applicant_id' => $application->applicant_id,
                'job_posting_id' => $application->job_posting_id,
                'application_id' => $application->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Interview scheduled successfully',
                'data' => [
                    'interview_scheduled_at' => $application->interview_scheduled_at,
                    'interview_location' => $application->interview_location,
                    'interview_type' => $application->interview_type,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule interview: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Schedule psikotes
     */
    public function schedulePsikotes(Request $request, Application $application): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'psikotes_scheduled_at' => 'required|date|after:now',
            'psikotes_location' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $application->update([
                'psikotes_scheduled_at' => $request->psikotes_scheduled_at,
                'psikotes_location' => $request->psikotes_location,
                'psikotes_notes' => $request->notes,
            ]);

            // Send WhatsApp notification
            $message = "📝 *JADWAL PSIKOTES*\n\n";
            $message .= "Halo {$application->applicant->full_name}!\n\n";
            $message .= "Anda telah dijadwalkan untuk mengikuti tes psikologi untuk posisi *{$application->jobPosting->title}* di {$application->jobPosting->company->name}.\n\n";
            $message .= "📋 *Detail Psikotes:*\n";
            $message .= "🗓️ Tanggal: " . \Carbon\Carbon::parse($request->psikotes_scheduled_at)->format('d M Y H:i') . "\n";
            $message .= "📍 Lokasi: {$request->psikotes_location}\n\n";
            $message .= "Harap datang tepat waktu dan bawa alat tulis.\n\n";
            $message .= "Semoga sukses! 🍀";

            \App\Models\WhatsAppLog::create([
                'session_id' => 'main_session',
                'phone_number' => $application->applicant->whatsapp_number,
                'message_type' => 'template',
                'message_content' => $message,
                'template_name' => 'psikotes_scheduled',
                'status' => 'pending',
                'context_type' => 'psikotes_scheduled',
                'context_id' => $application->id,
                'applicant_id' => $application->applicant_id,
                'job_posting_id' => $application->job_posting_id,
                'application_id' => $application->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Psikotes scheduled successfully',
                'data' => [
                    'psikotes_scheduled_at' => $application->psikotes_scheduled_at,
                    'psikotes_location' => $application->psikotes_location,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule psikotes: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Schedule medical checkup
     */
    public function scheduleMedical(Request $request, Application $application): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'medical_scheduled_at' => 'required|date|after:now',
            'medical_location' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $application->update([
                'medical_scheduled_at' => $request->medical_scheduled_at,
                'medical_location' => $request->medical_location,
                'medical_notes' => $request->notes,
            ]);

            // Send WhatsApp notification
            $message = "🏥 *JADWAL MEDICAL CHECKUP*\n\n";
            $message .= "Halo {$application->applicant->full_name}!\n\n";
            $message .= "Anda telah dijadwalkan untuk medical checkup untuk posisi *{$application->jobPosting->title}* di {$application->jobPosting->company->name}.\n\n";
            $message .= "📋 *Detail Medical:*\n";
            $message .= "🗓️ Tanggal: " . \Carbon\Carbon::parse($request->medical_scheduled_at)->format('d M Y H:i') . "\n";
            $message .= "📍 Lokasi: {$request->medical_location}\n\n";
            $message .= "Harap datang dalam kondisi puasa 10-12 jam untuk hasil yang akurat.\n\n";
            $message .= "Semoga sukses! 🍀";

            \App\Models\WhatsAppLog::create([
                'session_id' => 'main_session',
                'phone_number' => $application->applicant->whatsapp_number,
                'message_type' => 'template',
                'message_content' => $message,
                'template_name' => 'medical_scheduled',
                'status' => 'pending',
                'context_type' => 'medical_scheduled',
                'context_id' => $application->id,
                'applicant_id' => $application->applicant_id,
                'job_posting_id' => $application->job_posting_id,
                'application_id' => $application->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Medical checkup scheduled successfully',
                'data' => [
                    'medical_scheduled_at' => $application->medical_scheduled_at,
                    'medical_location' => $application->medical_location,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule medical checkup: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get application statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_applications' => Application::count(),
            'active_applications' => Application::active()->count(),
            'new_this_month' => Application::whereMonth('applied_at', now()->month)->count(),
            'by_stage' => Application::selectRaw('current_stage, COUNT(*) as count')
                ->groupBy('current_stage')
                ->pluck('count', 'current_stage'),
            'by_status' => Application::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'by_source' => Application::selectRaw('source, COUNT(*) as count')
                ->groupBy('source')
                ->pluck('count', 'source'),
            'success_rate' => [
                'total_applications' => Application::count(),
                'accepted' => Application::where('status', 'accepted')->count(),
                'placed' => Application::where('status', 'placed')->count(),
                'percentage' => Application::count() > 0 ? 
                    round((Application::where('status', 'placed')->count() / Application::count()) * 100, 2) : 0,
            ],
            'avg_processing_time' => Application::whereIn('status', ['accepted', 'rejected'])
                ->selectRaw('AVG(DATEDIFF(final_decision_at, applied_at)) as avg_days')
                ->value('avg_days') ?: 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}