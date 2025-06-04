<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Applicant;
use App\Models\Agent;
use App\Models\WhatsAppLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (!$user->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive. Please contact administrator.',
            ], 403);
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Load user profile based on role
        $userProfile = $this->getUserProfile($user);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $userProfile,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Register new applicant
     */
    public function registerApplicant(Request $request): JsonResponse
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
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'education_level' => 'required|in:sd,smp,sma,smk,d1,d2,d3,s1,s2,s3',
            'school_name' => 'required|string|max:255',
            'graduation_year' => 'required|integer|min:1990|max:' . date('Y'),
            'referral_code' => 'nullable|string|exists:agents,referral_code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Find agent if referral code provided
            $agent = null;
            if ($request->referral_code) {
                $agent = Agent::where('referral_code', $request->referral_code)->first();
            }

            // Create user account
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->nik), // Use NIK as default password
                'role' => 'applicant',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            // Create applicant profile
            $applicant = Applicant::create([
                'user_id' => $user->id,
                'agent_id' => $agent ? $agent->id : null,
                'nik' => $request->nik,
                'birth_date' => $request->birth_date,
                'birth_place' => $request->birth_place,
                'gender' => $request->gender,
                'address' => $request->address,
                'city' => $request->city,
                'province' => $request->province,
                'whatsapp_number' => $request->whatsapp_number,
                'education_level' => $request->education_level,
                'school_name' => $request->school_name,
                'graduation_year' => $request->graduation_year,
                'major' => $request->major,
                'registration_source' => $agent ? 'agent_referral' : 'direct',
                'status' => 'active',
                'work_status' => 'available',
            ]);

            // Update agent statistics
            if ($agent) {
                $agent->increment('total_referrals');
                $agent->addPoints(50, 'New applicant referral');
            }

            // Send welcome WhatsApp message
            $this->sendWelcomeMessage($applicant);

            // Create token for immediate login
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'user' => $this->getUserProfile($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user profile with role-specific data
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $userProfile = $this->getUserProfile($user);

        return response()->json([
            'success' => true,
            'data' => $userProfile,
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'current_password' => 'required_with:new_password|string',
            'new_password' => 'sometimes|required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Check current password if changing password
            if ($request->new_password) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect',
                    ], 422);
                }
                $user->password = Hash::make($request->new_password);
            }

            // Update basic profile fields
            if ($request->has('first_name')) {
                $user->first_name = $request->first_name;
            }
            if ($request->has('last_name')) {
                $user->last_name = $request->last_name;
            }
            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $this->getUserProfile($user),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile update failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Logout user (revoke current token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Logout from all devices (revoke all tokens)
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices',
        ]);
    }

    /**
     * Check if user is authenticated
     */
    public function check(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'authenticated' => true,
            'user' => $this->getUserProfile($request->user()),
        ]);
    }

    /**
     * Get user profile with role-specific data
     */
    private function getUserProfile(User $user): array
    {
        $profile = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'status' => $user->status,
            'profile_picture' => $user->profile_picture,
            'last_login_at' => $user->last_login_at,
            'created_at' => $user->created_at,
        ];

        // Add role-specific data
        switch ($user->role) {
            case 'applicant':
                if ($user->applicant) {
                    $profile['applicant'] = [
                        'id' => $user->applicant->id,
                        'nik' => $user->applicant->nik,
                        'birth_date' => $user->applicant->birth_date,
                        'age' => $user->applicant->age,
                        'gender' => $user->applicant->gender,
                        'city' => $user->applicant->city,
                        'province' => $user->applicant->province,
                        'education_level' => $user->applicant->education_level,
                        'work_status' => $user->applicant->work_status,
                        'profile_completed' => $user->applicant->isProfileCompleted(),
                        'total_applications' => $user->applicant->applications()->count(),
                        'active_applications' => $user->applicant->applications()->active()->count(),
                    ];
                }
                break;

            case 'agent':
                if ($user->agent) {
                    $profile['agent'] = [
                        'id' => $user->agent->id,
                        'agent_code' => $user->agent->agent_code,
                        'referral_code' => $user->agent->referral_code,
                        'level' => $user->agent->level,
                        'total_referrals' => $user->agent->total_referrals,
                        'successful_placements' => $user->agent->successful_placements,
                        'success_rate' => $user->agent->success_rate,
                        'total_points' => $user->agent->total_points,
                        'total_commission' => $user->agent->total_commission,
                        'qr_code_url' => $user->agent->qr_code_url,
                    ];
                }
                break;

            case 'hr_staff':
            case 'direktur':
            case 'super_admin':
                // Add admin-specific stats
                $profile['admin_stats'] = [
                    'total_applicants' => Applicant::count(),
                    'active_applicants' => Applicant::active()->count(),
                    'total_job_postings' => \App\Models\JobPosting::count(),
                    'active_job_postings' => \App\Models\JobPosting::active()->count(),
                ];
                break;
        }

        return $profile;
    }

    /**
     * Send welcome WhatsApp message to new applicant
     */
    private function sendWelcomeMessage(Applicant $applicant): void
    {
        $message = "🎉 *SELAMAT DATANG!* 🎉\n\n";
        $message .= "Halo {$applicant->full_name}!\n\n";
        $message .= "Terima kasih telah mendaftar di sistem kami. Akun Anda telah berhasil dibuat dengan detail berikut:\n\n";
        $message .= "📧 *Email:* {$applicant->email}\n";
        $message .= "🔑 *Password:* {$applicant->nik} (gunakan NIK Anda)\n\n";
        $message .= "⚠️ *PENTING:* Harap segera ganti password Anda setelah login pertama kali untuk keamanan akun.\n\n";
        $message .= "Kami akan menginformasikan lowongan pekerjaan yang sesuai dengan profil Anda melalui WhatsApp ini.\n\n";
        $message .= "Semoga beruntung dalam pencarian kerja! 🍀\n\n";
        $message .= "_Pesan otomatis dari sistem recruitment_";

        WhatsAppLog::create([
            'session_id' => 'main_session',
            'phone_number' => $applicant->whatsapp_number,
            'message_type' => 'template',
            'message_content' => $message,
            'template_name' => 'welcome_message',
            'template_variables' => [
                'applicant_name' => $applicant->full_name,
                'email' => $applicant->email,
                'password' => $applicant->nik,
            ],
            'status' => 'pending',
            'context_type' => 'welcome_message',
            'context_id' => $applicant->id,
            'applicant_id' => $applicant->id,
        ]);
    }
}