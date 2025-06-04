<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\Company;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class JobPostingController extends Controller
{
    /**
     * Display a listing of job postings
     */
    public function index(Request $request): JsonResponse
    {
        $query = JobPosting::with(['company', 'creator']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        if ($request->has('city')) {
            $query->city($request->city);
        }

        if ($request->has('salary_min')) {
            $query->where('salary_max', '>=', $request->salary_min);
        }

        if ($request->has('salary_max')) {
            $query->where('salary_min', '<=', $request->salary_max);
        }

        if ($request->has('deadline_from')) {
            $query->where('application_deadline', '>=', $request->deadline_from);
        }

        if ($request->has('deadline_to')) {
            $query->where('application_deadline', '<=', $request->deadline_to);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Special filters
        if ($request->has('active_only') && $request->boolean('active_only')) {
            $query->active();
        }

        if ($request->has('urgent_only') && $request->boolean('urgent_only')) {
            $query->urgent();
        }

        if ($request->has('featured_only') && $request->boolean('featured_only')) {
            $query->featured();
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Special sorting for priority
        if ($sortBy === 'priority') {
            $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low') ASC");
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $jobPostings = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $jobPostings,
        ]);
    }

    /**
     * Store a newly created job posting
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'title' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'employment_type' => 'required|in:magang,pkwt,project',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'work_location' => 'required|string|max:255',
            'work_city' => 'required|string|max:255',
            'work_province' => 'required|string|max:255',
            'work_arrangement' => 'required|in:onsite,remote,hybrid',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'salary_negotiable' => 'boolean',
            'contract_duration_months' => 'nullable|integer|min:1|max:60',
            'start_date' => 'nullable|date|after_or_equal:today',
            'application_deadline' => 'required|date|after:today',
            'required_education_levels' => 'required|array|min:1',
            'required_education_levels.*' => 'in:sd,smp,sma,smk,d1,d2,d3,s1,s2,s3',
            'min_age' => 'nullable|integer|min:16|max:65',
            'max_age' => 'nullable|integer|min:16|max:65|gte:min_age',
            'preferred_genders' => 'nullable|array',
            'preferred_genders.*' => 'in:male,female',
            'min_experience_months' => 'nullable|integer|min:0',
            'required_skills' => 'nullable|array',
            'preferred_skills' => 'nullable|array',
            'preferred_locations' => 'nullable|array',
            'total_positions' => 'required|integer|min:1|max:100',
            'priority' => 'required|in:low,normal,high,urgent',
            'is_featured' => 'boolean',
            'auto_broadcast_whatsapp' => 'boolean',
            'internal_notes' => 'nullable|string',
            'matching_algorithm_weights' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $jobData = $request->all();
            $jobData['created_by'] = Auth::id();
            $jobData['status'] = 'draft'; // Start as draft

            $jobPosting = JobPosting::create($jobData);

            // If auto-publish is requested, publish immediately
            if ($request->has('publish_immediately') && $request->boolean('publish_immediately')) {
                $jobPosting->update([
                    'status' => 'published',
                    'published_at' => now(),
                ]);

                // Trigger WhatsApp broadcast if enabled
                if ($jobPosting->auto_broadcast_whatsapp) {
                    $this->broadcastJobPosting($jobPosting);
                }
            }

            $jobPosting->load(['company', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Job posting created successfully',
                'data' => $jobPosting,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create job posting: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified job posting
     */
    public function show(JobPosting $jobPosting): JsonResponse
    {
        $jobPosting->load([
            'company',
            'creator',
            'applications.applicant.user',
            'applications' => function ($query) {
                $query->orderBy('applied_at', 'desc');
            }
        ]);

        // Add computed data
        $data = $jobPosting->toArray();
        $data['days_until_deadline'] = $jobPosting->days_until_deadline;
        $data['is_accepting_applications'] = $jobPosting->isAcceptingApplications();
        $data['success_rate'] = $jobPosting->success_rate;
        $data['remaining_positions'] = $jobPosting->remaining_positions;

        // Get matching applicants preview
        if ($jobPosting->status === 'published') {
            $matchingApplicants = $jobPosting->findMatchingApplicants(10);
            $data['matching_applicants_preview'] = $matchingApplicants->map(function ($applicant) {
                return [
                    'id' => $applicant->id,
                    'name' => $applicant->full_name,
                    'education_level' => $applicant->education_level,
                    'city' => $applicant->city,
                    'work_experience_years' => $applicant->work_experience_years,
                    'matching_score' => $applicant->matching_score ?? 0,
                    'matching_details' => $applicant->matching_details ?? [],
                ];
            });
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Update the specified job posting
     */
    public function update(Request $request, JobPosting $jobPosting): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'sometimes|required|exists:companies,id',
            'title' => 'sometimes|required|string|max:255',
            'position' => 'sometimes|required|string|max:255',
            'department' => 'sometimes|nullable|string|max:255',
            'employment_type' => 'sometimes|required|in:magang,pkwt,project',
            'description' => 'sometimes|required|string',
            'responsibilities' => 'sometimes|nullable|string',
            'requirements' => 'sometimes|nullable|string',
            'benefits' => 'sometimes|nullable|string',
            'work_location' => 'sometimes|required|string|max:255',
            'work_city' => 'sometimes|required|string|max:255',
            'work_province' => 'sometimes|required|string|max:255',
            'work_arrangement' => 'sometimes|required|in:onsite,remote,hybrid',
            'salary_min' => 'sometimes|nullable|numeric|min:0',
            'salary_max' => 'sometimes|nullable|numeric|min:0|gte:salary_min',
            'salary_negotiable' => 'sometimes|boolean',
            'contract_duration_months' => 'sometimes|nullable|integer|min:1|max:60',
            'start_date' => 'sometimes|nullable|date|after_or_equal:today',
            'application_deadline' => 'sometimes|required|date|after:today',
            'required_education_levels' => 'sometimes|required|array|min:1',
            'required_education_levels.*' => 'in:sd,smp,sma,smk,d1,d2,d3,s1,s2,s3',
            'min_age' => 'sometimes|nullable|integer|min:16|max:65',
            'max_age' => 'sometimes|nullable|integer|min:16|max:65|gte:min_age',
            'preferred_genders' => 'sometimes|nullable|array',
            'preferred_genders.*' => 'in:male,female',
            'min_experience_months' => 'sometimes|nullable|integer|min:0',
            'required_skills' => 'sometimes|nullable|array',
            'preferred_skills' => 'sometimes|nullable|array',
            'preferred_locations' => 'sometimes|nullable|array',
            'total_positions' => 'sometimes|required|integer|min:1|max:100',
            'status' => 'sometimes|required|in:draft,published,paused,closed,cancelled',
            'priority' => 'sometimes|required|in:low,normal,high,urgent',
            'is_featured' => 'sometimes|boolean',
            'auto_broadcast_whatsapp' => 'sometimes|boolean',
            'internal_notes' => 'sometimes|nullable|string',
            'matching_algorithm_weights' => 'sometimes|nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $oldStatus = $jobPosting->status;
            $jobPosting->update($request->all());

            // If status changed to published, set published_at
            if ($oldStatus !== 'published' && $jobPosting->status === 'published') {
                $jobPosting->update(['published_at' => now()]);

                // Trigger WhatsApp broadcast if enabled
                if ($jobPosting->auto_broadcast_whatsapp) {
                    $this->broadcastJobPosting($jobPosting);
                }
            }

            // If status changed to closed, set closed_at
            if ($oldStatus !== 'closed' && $jobPosting->status === 'closed') {
                $jobPosting->update(['closed_at' => now()]);
            }

            $jobPosting->load(['company', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Job posting updated successfully',
                'data' => $jobPosting,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update job posting: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified job posting
     */
    public function destroy(JobPosting $jobPosting): JsonResponse
    {
        try {
            // Check if job has active applications
            $activeApplicationsCount = $jobPosting->applications()->active()->count();
            
            if ($activeApplicationsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete job posting with {$activeApplicationsCount} active applications. Please close the job posting instead.",
                ], 422);
            }

            $jobPosting->delete();

            return response()->json([
                'success' => true,
                'message' => 'Job posting deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete job posting: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Publish a job posting
     */
    public function publish(JobPosting $jobPosting): JsonResponse
    {
        if ($jobPosting->status === 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Job posting is already published',
            ], 422);
        }

        try {
            $jobPosting->update([
                'status' => 'published',
                'published_at' => now(),
            ]);

            // Trigger WhatsApp broadcast if enabled
            if ($jobPosting->auto_broadcast_whatsapp) {
                $broadcastResult = $this->broadcastJobPosting($jobPosting);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Job posting published successfully',
                    'data' => [
                        'job_posting' => $jobPosting,
                        'broadcast_result' => $broadcastResult,
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Job posting published successfully',
                'data' => $jobPosting,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish job posting: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Close a job posting
     */
    public function close(JobPosting $jobPosting): JsonResponse
    {
        if ($jobPosting->status === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Job posting is already closed',
            ], 422);
        }

        try {
            $jobPosting->update([
                'status' => 'closed',
                'closed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Job posting closed successfully',
                'data' => $jobPosting,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to close job posting: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get matching applicants for a job posting
     */
    public function matchingApplicants(Request $request, JobPosting $jobPosting): JsonResponse
    {
        $limit = $request->get('limit', 50);
        $matchingApplicants = $jobPosting->findMatchingApplicants($limit);

        $applicantsData = $matchingApplicants->map(function ($applicant) {
            return [
                'id' => $applicant->id,
                'name' => $applicant->full_name,
                'email' => $applicant->email,
                'phone' => $applicant->user->phone,
                'whatsapp_number' => $applicant->whatsapp_number,
                'age' => $applicant->age,
                'gender' => $applicant->gender,
                'education_level' => $applicant->education_level,
                'school_name' => $applicant->school_name,
                'graduation_year' => $applicant->graduation_year,
                'city' => $applicant->city,
                'province' => $applicant->province,
                'work_experience_years' => $applicant->work_experience_years,
                'technical_skills' => $applicant->technical_skills,
                'work_status' => $applicant->work_status,
                'matching_score' => $applicant->matching_score ?? 0,
                'matching_details' => $applicant->matching_details ?? [],
                'has_applied' => $applicant->applications()->where('job_posting_id', $jobPosting->id)->exists(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'job_posting' => [
                    'id' => $jobPosting->id,
                    'title' => $jobPosting->title,
                    'company' => $jobPosting->company->name,
                ],
                'matching_applicants' => $applicantsData,
                'total_matches' => $applicantsData->count(),
                'criteria_used' => [
                    'education_levels' => $jobPosting->required_education_levels,
                    'age_range' => [
                        'min' => $jobPosting->min_age,
                        'max' => $jobPosting->max_age,
                    ],
                    'min_experience_months' => $jobPosting->min_experience_months,
                    'required_skills' => $jobPosting->required_skills,
                    'preferred_locations' => $jobPosting->preferred_locations,
                    'preferred_genders' => $jobPosting->preferred_genders,
                ],
            ],
        ]);
    }

    /**
     * Broadcast job posting to matching applicants via WhatsApp
     */
    public function broadcastWhatsApp(JobPosting $jobPosting): JsonResponse
    {
        if ($jobPosting->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Only published job postings can be broadcasted',
            ], 422);
        }

        try {
            $result = $this->broadcastJobPosting($jobPosting);

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp broadcast initiated successfully',
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to broadcast job posting: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get job posting statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_jobs' => JobPosting::count(),
            'published_jobs' => JobPosting::published()->count(),
            'active_jobs' => JobPosting::active()->count(),
            'urgent_jobs' => JobPosting::urgent()->count(),
            'featured_jobs' => JobPosting::featured()->count(),
            'new_this_month' => JobPosting::whereMonth('created_at', now()->month)->count(),
            'by_status' => JobPosting::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'by_employment_type' => JobPosting::selectRaw('employment_type, COUNT(*) as count')
                ->groupBy('employment_type')
                ->pluck('count', 'employment_type'),
            'by_priority' => JobPosting::selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority'),
            'by_company' => JobPosting::with('company')
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
            'application_stats' => [
                'total_applications' => \App\Models\Application::count(),
                'avg_applications_per_job' => round(JobPosting::withCount('applications')->avg('applications_count'), 2),
                'success_rate' => JobPosting::selectRaw('AVG(total_hired / NULLIF(total_applications, 0) * 100) as rate')->value('rate') ?? 0,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Helper method to broadcast job posting
     */
    private function broadcastJobPosting(JobPosting $jobPosting): array
    {
        return $jobPosting->broadcastToMatchingApplicants();
    }
}