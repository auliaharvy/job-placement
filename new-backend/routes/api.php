<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\ApplicationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {

    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register/applicant', [AuthController::class, 'registerApplicant']);
    });

    // Public job listings (for applicant portal)
    Route::get('jobs/public', [JobPostingController::class, 'index']);
    Route::get('jobs/public/{jobPosting}', [JobPostingController::class, 'show']);

    // QR Code generation for registration
    Route::post('qr-code/generate', [ApplicantController::class, 'generateQRCode']);
});

// Protected routes (authentication required)
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
        Route::get('check', [AuthController::class, 'check']);
    });

    // Dashboard routes
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Applicant routes
    Route::prefix('applicants')->group(function () {
        Route::get('/', [ApplicantController::class, 'index'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('/', [ApplicantController::class, 'store'])->middleware('role:super_admin,direktur,hr_staff');
        Route::get('statistics', [ApplicantController::class, 'statistics'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('bulk-import', [ApplicantController::class, 'bulkImport'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('export', [ApplicantController::class, 'export'])->middleware('role:super_admin,direktur,hr_staff');

        Route::get('{applicant}', [ApplicantController::class, 'show']);
        Route::put('{applicant}', [ApplicantController::class, 'update']);
        Route::delete('{applicant}', [ApplicantController::class, 'destroy'])->middleware('role:super_admin,direktur');
        Route::post('{applicant}/upload-document', [ApplicantController::class, 'uploadDocument']);
    });

    // Job Posting routes
    Route::prefix('jobs')->group(function () {
        Route::get('/', [JobPostingController::class, 'index']);
        Route::post('/', [JobPostingController::class, 'store'])->middleware('role:super_admin,direktur,hr_staff');
        Route::get('statistics', [JobPostingController::class, 'statistics'])->middleware('role:super_admin,direktur,hr_staff');

        Route::get('{jobPosting}', [JobPostingController::class, 'show']);
        Route::put('{jobPosting}', [JobPostingController::class, 'update'])->middleware('role:super_admin,direktur,hr_staff');
        Route::delete('{jobPosting}', [JobPostingController::class, 'destroy'])->middleware('role:super_admin,direktur');

        // Job posting actions
        Route::post('{jobPosting}/publish', [JobPostingController::class, 'publish'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('{jobPosting}/close', [JobPostingController::class, 'close'])->middleware('role:super_admin,direktur,hr_staff');
        Route::get('{jobPosting}/matching-applicants', [JobPostingController::class, 'matchingApplicants'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('{jobPosting}/broadcast-whatsapp', [JobPostingController::class, 'broadcastWhatsApp'])->middleware('role:super_admin,direktur,hr_staff');
    });

    // Application routes
    Route::prefix('applications')->group(function () {
        Route::get('/', [ApplicationController::class, 'index']);
        Route::post('/', [ApplicationController::class, 'store']);
        Route::get('statistics', [ApplicationController::class, 'statistics'])->middleware('role:super_admin,direktur,hr_staff');

        Route::get('{application}', [ApplicationController::class, 'show']);
        Route::put('{application}', [ApplicationController::class, 'update'])->middleware('role:super_admin,direktur,hr_staff');
        Route::delete('{application}', [ApplicationController::class, 'destroy'])->middleware('role:super_admin,direktur');

        // Application stage management
        Route::post('{application}/progress', [ApplicationController::class, 'progressStage'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('{application}/reject', [ApplicationController::class, 'reject'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('{application}/accept', [ApplicationController::class, 'accept'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('{application}/schedule-interview', [ApplicationController::class, 'scheduleInterview'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('{application}/schedule-psikotes', [ApplicationController::class, 'schedulePsikotes'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('{application}/schedule-medical', [ApplicationController::class, 'scheduleMedical'])->middleware('role:super_admin,direktur,hr_staff');
    });

    // Placement routes
    Route::prefix('placements')->group(function () {
        Route::get('/', [PlacementController::class, 'index'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('/', [PlacementController::class, 'store'])->middleware('role:super_admin,direktur,hr_staff');
        Route::get('statistics', [PlacementController::class, 'statistics'])->middleware('role:super_admin,direktur,hr_staff');
        Route::get('expiring', [PlacementController::class, 'expiring'])->middleware('role:super_admin,direktur,hr_staff');

        Route::get('{placement}', [PlacementController::class, 'show']);
        Route::put('{placement}', [PlacementController::class, 'update'])->middleware('role:super_admin,direktur,hr_staff');
        Route::delete('{placement}', [PlacementController::class, 'destroy'])->middleware('role:super_admin,direktur');

        // Placement actions
        Route::post('{placement}/terminate', [PlacementController::class, 'terminate'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('{placement}/complete', [PlacementController::class, 'complete'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('{placement}/add-review', [PlacementController::class, 'addPerformanceReview'])->middleware('role:super_admin,direktur,hr_staff');
    });

    // Company routes
    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('/', [CompanyController::class, 'store'])->middleware('role:super_admin,direktur,hr_staff');
        Route::get('statistics', [CompanyController::class, 'statistics'])->middleware('role:super_admin,direktur,hr_staff');

        Route::get('{company}', [CompanyController::class, 'show']);
        Route::put('{company}', [CompanyController::class, 'update'])->middleware('role:super_admin,direktur,hr_staff');
        Route::delete('{company}', [CompanyController::class, 'destroy'])->middleware('role:super_admin,direktur');
    });

    // Agent routes
    Route::prefix('agents')->group(function () {
        Route::get('/', [AgentController::class, 'index'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('/', [AgentController::class, 'store'])->middleware('role:super_admin,direktur,hr_staff');
        Route::get('statistics', [AgentController::class, 'statistics'])->middleware('role:super_admin,direktur,hr_staff');
        Route::get('leaderboard', [AgentController::class, 'leaderboard'])->middleware('role:super_admin,direktur,hr_staff');

        Route::get('{agent}', [AgentController::class, 'show']);
        Route::put('{agent}', [AgentController::class, 'update'])->middleware('role:super_admin,direktur,hr_staff');
        Route::delete('{agent}', [AgentController::class, 'destroy'])->middleware('role:super_admin,direktur');

        // Agent actions
        Route::get('{agent}/qr-code', [AgentController::class, 'getQRCode']);
        Route::get('{agent}/performance', [AgentController::class, 'getPerformance']);
        Route::post('{agent}/add-points', [AgentController::class, 'addPoints'])->middleware('role:super_admin,direktur,hr_staff');
    });

    // WhatsApp routes
    Route::prefix('whatsapp')->group(function () {
        Route::get('status', [\App\Http\Controllers\WhatsAppController::class, 'status'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('start-session', [\App\Http\Controllers\WhatsAppController::class, 'startSession'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('stop-session', [\App\Http\Controllers\WhatsAppController::class, 'stopSession'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('send-test-message', [\App\Http\Controllers\WhatsAppController::class, 'sendTestMessage'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('send-test-image', [\App\Http\Controllers\WhatsAppController::class, 'sendTestImage'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('send-test-document', [\App\Http\Controllers\WhatsAppController::class, 'sendTestDocument'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('test-workflow', [\App\Http\Controllers\WhatsAppController::class, 'testWorkflow'])->middleware('role:super_admin,direktur,hr_staff');
        Route::get('logs', [\App\Http\Controllers\WhatsAppController::class, 'logs'])->middleware('role:super_admin,direktur,hr_staff');
        Route::get('statistics', [\App\Http\Controllers\WhatsAppController::class, 'statistics'])->middleware('role:super_admin,direktur,hr_staff');
    });

    // Report routes
    Route::prefix('reports')->group(function () {
        Route::get('dashboard', [ReportController::class, 'dashboard'])->middleware('role:super_admin,direktur,hr_staff');
        Route::get('applicants', [ReportController::class, 'applicants'])->middleware('role:super_admin,direktur,hr_staff');
        Route::get('placements', [ReportController::class, 'placements'])->middleware('role:super_admin,direktur,hr_staff');
        Route::get('agents', [ReportController::class, 'agents'])->middleware('role:super_admin,direktur,hr_staff');
        Route::get('companies', [ReportController::class, 'companies'])->middleware('role:super_admin,direktur,hr_staff');
        Route::post('export', [ReportController::class, 'export'])->middleware('role:super_admin,direktur,hr_staff');
    });

    // User management routes (Admin only)
    Route::prefix('users')->middleware('role:super_admin,direktur')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('{user}', [UserController::class, 'show']);
        Route::put('{user}', [UserController::class, 'update']);
        Route::delete('{user}', [UserController::class, 'destroy'])->middleware('role:super_admin');
        Route::post('{user}/reset-password', [UserController::class, 'resetPassword']);
        Route::post('{user}/toggle-status', [UserController::class, 'toggleStatus']);
    });

    // System settings routes (Super Admin only)
    Route::prefix('settings')->middleware('role:super_admin')->group(function () {
        Route::get('/', [SettingsController::class, 'index']);
        Route::put('/', [SettingsController::class, 'update']);
        Route::get('backup', [SettingsController::class, 'backup']);
        Route::post('restore', [SettingsController::class, 'restore']);
        Route::get('logs', [SettingsController::class, 'logs']);
    });
});

// Testing routes (for development)
Route::prefix('v1/test')->group(function () {
    Route::get('health', [\App\Http\Controllers\TestController::class, 'healthCheck']);
    Route::get('job-matching', [\App\Http\Controllers\TestController::class, 'testJobMatching']);
    Route::get('whatsapp', [\App\Http\Controllers\TestController::class, 'testWhatsApp']);
    Route::get('models', [\App\Http\Controllers\TestController::class, 'testModels']);
    Route::get('workflow', [\App\Http\Controllers\TestController::class, 'testWorkflow']);
    Route::post('generate-test-data', [\App\Http\Controllers\TestController::class, 'generateTestData']);
    
    // WhatsApp Testing Routes (No Auth Required for Testing)
    Route::prefix('whatsapp')->group(function () {
        Route::get('status', [\App\Http\Controllers\WhatsAppController::class, 'status']);
        Route::post('start-session', [\App\Http\Controllers\WhatsAppController::class, 'startSession']);
        Route::post('send-test-message', [\App\Http\Controllers\WhatsAppController::class, 'sendTestMessage']);
        Route::post('test-workflow', [\App\Http\Controllers\WhatsAppController::class, 'testWorkflow']);
    });
});

// Fallback route for undefined API endpoints
Route::fallback(function() {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'error' => 'The requested API endpoint does not exist.'
    ], 404);
});
