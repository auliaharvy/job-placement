<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ApplicantController extends Controller
{
    /**
     * Display a listing of applicants
     */
    public function index(Request $request): JsonResponse
    {
        $query = Applicant::with(['user', 'agent.user']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('work_status')) {
            $query->where('work_status', $request->work_status);
        }

        if ($request->has('education_level')) {
            $query->where('education_level', $request->education_level);
        }

        if ($request->has('city')) {
            $query->city($request->city);
        }

        if ($request->has('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        if ($request->has('age_min') || $request->has('age_max')) {
            $query->ageRange($request->age_min, $request->age_max);
        }

        if ($request->has('experience_min')) {
            $query->minExperience($request->experience_min);
        }

        if ($request->has('skills')) {
            $skills = is_array($request->skills) ? $request->skills : explode(',', $request->skills);
            $query->hasSkills($skills);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $applicants = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $applicants,
        ]);
    }

    /**
     * Store a newly created applicant
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'nik' => 'required|string|size:16|unique:applicants,nik',
            'birth_date' => 'required|date|before:today',
            'birth_place' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'religion' => 'nullable|string|max:255',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'height' => 'nullable|integer|min:100|max:250',
            'weight' => 'nullable|integer|min:30|max:200',
            'blood_type' => 'nullable|string|max:5',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'whatsapp_number' => 'required|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:255',
            'education_level' => 'required|in:sd,smp,sma,smk,d1,d2,d3,s1,s2,s3',
            'school_name' => 'required|string|max:255',
            'major' => 'nullable|string|max:255',
            'graduation_year' => 'required|integer|min:1990|max:' . date('Y'),
            'gpa' => 'nullable|numeric|min:0|max:4',
            'work_experience' => 'nullable|array',
            'skills' => 'nullable|array',
            'total_work_experience_months' => 'nullable|integer|min:0',
            'preferred_positions' => 'nullable|array',
            'preferred_locations' => 'nullable|array',
            'expected_salary_min' => 'nullable|numeric|min:0',
            'expected_salary_max' => 'nullable|numeric|min:0',
            'agent_id' => 'nullable|exists:agents,id',
            'registration_source' => 'nullable|string',
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
            // Create user account
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->nik),
                'role' => 'applicant',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            // Create applicant profile
            $applicantData = $request->only([
                'nik', 'birth_date', 'birth_place', 'gender', 'religion', 'marital_status',
                'height', 'weight', 'blood_type', 'address', 'city', 'province', 'postal_code',
                'whatsapp_number', 'emergency_contact_name', 'emergency_contact_phone',
                'emergency_contact_relation', 'education_level', 'school_name', 'major',
                'graduation_year', 'gpa', 'work_experience', 'skills', 'total_work_experience_months',
                'preferred_positions', 'preferred_locations', 'expected_salary_min',
                'expected_salary_max', 'agent_id', 'registration_source', 'notes'
            ]);

            $applicantData['user_id'] = $user->id;
            $applicantData['status'] = 'active';
            $applicantData['work_status'] = 'available';

            $applicant = Applicant::create($applicantData);

            // Update agent statistics if agent is assigned
            if ($request->agent_id) {
                $agent = Agent::find($request->agent_id);
                if ($agent) {
                    $agent->increment('total_referrals');
                    $agent->addPoints(50, 'New applicant registration');
                }
            }

            $applicant->load(['user', 'agent.user']);

            return response()->json([
                'success' => true,
                'message' => 'Applicant created successfully',
                'data' => $applicant,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create applicant: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified applicant
     */
    public function show(Applicant $applicant): JsonResponse
    {
        $applicant->load([
            'user',
            'agent.user',
            'applications.jobPosting.company',
            'placements.company'
        ]);

        // Add additional computed data
        $data = $applicant->toArray();
        $data['applications_count'] = $applicant->applications()->count();
        $data['active_applications_count'] = $applicant->applications()->active()->count();
        $data['placements_count'] = $applicant->placements()->count();
        $data['active_placements_count'] = $applicant->placements()->active()->count();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Update the specified applicant
     */
    public function update(Request $request, Applicant $applicant): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'birth_date' => 'sometimes|required|date|before:today',
            'birth_place' => 'sometimes|required|string|max:255',
            'gender' => 'sometimes|required|in:male,female',
            'religion' => 'sometimes|nullable|string|max:255',
            'marital_status' => 'sometimes|required|in:single,married,divorced,widowed',
            'height' => 'sometimes|nullable|integer|min:100|max:250',
            'weight' => 'sometimes|nullable|integer|min:30|max:200',
            'blood_type' => 'sometimes|nullable|string|max:5',
            'address' => 'sometimes|required|string',
            'city' => 'sometimes|required|string|max:255',
            'province' => 'sometimes|required|string|max:255',
            'postal_code' => 'sometimes|nullable|string|max:10',
            'whatsapp_number' => 'sometimes|required|string|max:20',
            'emergency_contact_name' => 'sometimes|nullable|string|max:255',
            'emergency_contact_phone' => 'sometimes|nullable|string|max:20',
            'emergency_contact_relation' => 'sometimes|nullable|string|max:255',
            'education_level' => 'sometimes|required|in:sd,smp,sma,smk,d1,d2,d3,s1,s2,s3',
            'school_name' => 'sometimes|required|string|max:255',
            'major' => 'sometimes|nullable|string|max:255',
            'graduation_year' => 'sometimes|required|integer|min:1990|max:' . date('Y'),
            'gpa' => 'sometimes|nullable|numeric|min:0|max:4',
            'work_experience' => 'sometimes|nullable|array',
            'skills' => 'sometimes|nullable|array',
            'total_work_experience_months' => 'sometimes|nullable|integer|min:0',
            'preferred_positions' => 'sometimes|nullable|array',
            'preferred_locations' => 'sometimes|nullable|array',
            'expected_salary_min' => 'sometimes|nullable|numeric|min:0',
            'expected_salary_max' => 'sometimes|nullable|numeric|min:0',
            'status' => 'sometimes|required|in:active,inactive,blacklisted',
            'work_status' => 'sometimes|required|in:available,working,not_available',
            'available_from' => 'sometimes|nullable|date',
            'notes' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Update user data if provided
            $userData = $request->only(['first_name', 'last_name', 'phone']);
            if (!empty($userData)) {
                $applicant->user->update($userData);
            }

            // Update applicant data
            $applicantData = $request->except(['first_name', 'last_name', 'phone']);
            $applicant->update($applicantData);

            // Mark profile as completed if not already
            if (!$applicant->profile_completed_at && $this->isProfileComplete($applicant)) {
                $applicant->update(['profile_completed_at' => now()]);
            }

            $applicant->load(['user', 'agent.user']);

            return response()->json([
                'success' => true,
                'message' => 'Applicant updated successfully',
                'data' => $applicant,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update applicant: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified applicant
     */
    public function destroy(Applicant $applicant): JsonResponse
    {
        try {
            // Soft delete the user account (which will cascade to applicant)
            $applicant->user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Applicant deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete applicant: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload document for applicant
     */
    public function uploadDocument(Request $request, Applicant $applicant): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:ktp,ijazah,cv,photo,certificate',
            'file' => 'required|file|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $file = $request->file('file');
            $documentType = $request->document_type;
            
            // Validate file type based on document type
            $allowedTypes = [
                'ktp' => ['jpg', 'jpeg', 'png'],
                'ijazah' => ['jpg', 'jpeg', 'png', 'pdf'],
                'cv' => ['pdf', 'doc', 'docx'],
                'photo' => ['jpg', 'jpeg', 'png'],
                'certificate' => ['jpg', 'jpeg', 'png', 'pdf'],
            ];

            $fileExtension = $file->getClientOriginalExtension();
            if (!in_array(strtolower($fileExtension), $allowedTypes[$documentType])) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid file type for {$documentType}. Allowed: " . implode(', ', $allowedTypes[$documentType]),
                ], 422);
            }

            // Generate filename
            $filename = "applicant_{$applicant->id}_{$documentType}_" . time() . "." . $fileExtension;
            
            // Store file
            $path = $file->storeAs("applicants/{$applicant->id}/documents", $filename, 'public');

            // Update applicant record
            if ($documentType === 'certificate') {
                // Handle multiple certificates
                $certificates = $applicant->certificate_files ?? [];
                $certificates[] = $path;
                $applicant->update(['certificate_files' => $certificates]);
            } else {
                // Single document fields
                $applicant->update(["{$documentType}_file" => $path]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'data' => [
                    'document_type' => $documentType,
                    'file_path' => $path,
                    'file_url' => Storage::url($path),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get applicant statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_applicants' => Applicant::count(),
            'active_applicants' => Applicant::active()->count(),
            'available_applicants' => Applicant::available()->count(),
            'working_applicants' => Applicant::where('work_status', 'working')->count(),
            'new_this_month' => Applicant::whereMonth('created_at', now()->month)->count(),
            'by_education_level' => Applicant::selectRaw('education_level, COUNT(*) as count')
                ->groupBy('education_level')
                ->pluck('count', 'education_level'),
            'by_city' => Applicant::selectRaw('city, COUNT(*) as count')
                ->groupBy('city')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('count', 'city'),
            'by_agent' => Applicant::with('agent.user')
                ->whereNotNull('agent_id')
                ->selectRaw('agent_id, COUNT(*) as count')
                ->groupBy('agent_id')
                ->orderByDesc('count')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'agent_name' => $item->agent->full_name ?? 'Unknown',
                        'agent_code' => $item->agent->agent_code ?? 'Unknown',
                        'count' => $item->count,
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Bulk import applicants
     */
    public function bulkImport(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,xlsx',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // This would typically use a package like Laravel Excel
            // For now, we'll return a placeholder response
            return response()->json([
                'success' => true,
                'message' => 'Bulk import functionality to be implemented',
                'data' => [
                    'imported_count' => 0,
                    'failed_count' => 0,
                    'errors' => [],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to import applicants: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export applicants
     */
    public function export(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'format' => 'required|in:csv,xlsx,pdf',
            'filters' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // This would typically generate an export file
            // For now, we'll return a placeholder response
            return response()->json([
                'success' => true,
                'message' => 'Export functionality to be implemented',
                'data' => [
                    'download_url' => '#',
                    'file_name' => 'applicants_export.' . $request->format,
                    'total_records' => 0,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export applicants: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate QR code for applicant registration
     */
    public function generateQRCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'agent_id' => 'nullable|exists:agents,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $agent = null;
            if ($request->agent_id) {
                $agent = Agent::find($request->agent_id);
            }

            // Generate registration URL
            $registrationUrl = config('app.url') . '/register';
            if ($agent) {
                $registrationUrl .= '?ref=' . $agent->referral_code;
            }

            // In a real implementation, you would generate a QR code image
            // For now, we'll return the URL and QR code data
            return response()->json([
                'success' => true,
                'data' => [
                    'registration_url' => $registrationUrl,
                    'qr_code_data' => $registrationUrl,
                    'agent' => $agent ? [
                        'id' => $agent->id,
                        'name' => $agent->full_name,
                        'code' => $agent->agent_code,
                        'referral_code' => $agent->referral_code,
                    ] : null,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QR code: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if applicant profile is complete
     */
    private function isProfileComplete(Applicant $applicant): bool
    {
        $requiredFields = [
            'nik', 'birth_date', 'birth_place', 'gender', 'address', 'city', 'province',
            'whatsapp_number', 'education_level', 'school_name', 'graduation_year'
        ];

        foreach ($requiredFields as $field) {
            if (empty($applicant->$field)) {
                return false;
            }
        }

        return true;
    }
}