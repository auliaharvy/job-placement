<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_posting_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_id')->nullable()->constrained()->onDelete('set null'); // If applied through agent
            
            // Application Basic Info
            $table->string('application_number')->unique(); // Format: APP-2024-001234
            $table->enum('source', ['direct', 'agent_referral', 'whatsapp_broadcast', 'walk_in'])->default('direct');
            $table->timestamp('applied_at');
            
            // Selection Process Stages
            $table->enum('current_stage', [
                'applied',           // 1. Just applied
                'screening',         // 2. Initial screening
                'psikotes',         // 3. Psychological test
                'interview',        // 4. Interview process
                'medical',          // 5. Medical checkup
                'final_review',     // 6. Final review
                'accepted',         // 7. Accepted for placement
                'rejected'          // 8. Rejected at any stage
            ])->default('applied');
            
            // Application Status
            $table->enum('status', ['active', 'withdrawn', 'rejected', 'accepted', 'placed'])->default('active');
            
            // Matching Score
            $table->decimal('matching_score', 5, 2)->nullable(); // 0-100 percentage
            $table->json('matching_details')->nullable(); // Breakdown of matching criteria
            
            // Selection Process Details
            $table->json('selection_process')->nullable(); // Stores all stage details and results
            
            // Stage: Screening
            $table->timestamp('screening_scheduled_at')->nullable();
            $table->timestamp('screening_completed_at')->nullable();
            $table->enum('screening_result', ['pass', 'fail', 'pending'])->nullable();
            $table->text('screening_notes')->nullable();
            $table->foreignId('screened_by')->nullable()->constrained('users');
            
            // Stage: Psikotes
            $table->timestamp('psikotes_scheduled_at')->nullable();
            $table->timestamp('psikotes_completed_at')->nullable();
            $table->enum('psikotes_result', ['pass', 'fail', 'pending'])->nullable();
            $table->decimal('psikotes_score', 5, 2)->nullable();
            $table->text('psikotes_notes')->nullable();
            $table->string('psikotes_location')->nullable();
            
            // Stage: Interview
            $table->timestamp('interview_scheduled_at')->nullable();
            $table->timestamp('interview_completed_at')->nullable();
            $table->enum('interview_result', ['pass', 'fail', 'pending'])->nullable();
            $table->decimal('interview_score', 5, 2)->nullable();
            $table->text('interview_notes')->nullable();
            $table->foreignId('interviewed_by')->nullable()->constrained('users');
            $table->string('interview_location')->nullable();
            $table->enum('interview_type', ['online', 'offline'])->default('offline');
            
            // Stage: Medical
            $table->timestamp('medical_scheduled_at')->nullable();
            $table->timestamp('medical_completed_at')->nullable();
            $table->enum('medical_result', ['pass', 'fail', 'pending'])->nullable();
            $table->text('medical_notes')->nullable();
            $table->string('medical_location')->nullable();
            $table->json('medical_details')->nullable(); // Detailed medical report
            
            // Final Decision
            $table->timestamp('final_decision_at')->nullable();
            $table->foreignId('final_decision_by')->nullable()->constrained('users');
            $table->text('final_decision_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Notifications & Communication
            $table->json('notification_log')->nullable(); // WhatsApp/email notification history
            $table->timestamp('last_notification_sent_at')->nullable();
            
            // Additional Info
            $table->text('applicant_notes')->nullable(); // Notes from applicant
            $table->text('internal_notes')->nullable(); // Internal notes from HR
            $table->json('documents_submitted')->nullable(); // List of documents submitted
            $table->boolean('documents_verified')->default(false);
            
            $table->timestamps();
            
            // Indexes for efficient querying
            $table->index(['current_stage', 'status']);
            $table->index(['job_posting_id', 'status']);
            $table->index(['applicant_id', 'applied_at']);
            $table->index('application_number');
            $table->index(['agent_id', 'status']);
            
            // Unique constraint to prevent duplicate applications
            $table->unique(['applicant_id', 'job_posting_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};