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
        Schema::create('placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('applicant_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_posting_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_id')->nullable()->constrained()->onDelete('set null');
            
            // Placement Basic Info
            $table->string('placement_number')->unique(); // Format: PLC-2024-001234
            $table->string('employee_id')->nullable(); // Company's employee ID
            $table->string('position_title');
            $table->string('department');
            $table->string('work_location');
            
            // Contract Details
            $table->enum('contract_type', ['magang', 'pkwt', 'project']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('contract_duration_months')->nullable();
            $table->decimal('salary', 12, 2);
            $table->text('benefits')->nullable();
            $table->json('contract_terms')->nullable(); // Additional contract terms
            
            // Status & Tracking
            $table->enum('status', [
                'pending_start',     // Accepted but not started yet
                'active',           // Currently working
                'completed',        // Contract completed successfully
                'terminated',       // Contract terminated early
                'expired',          // Contract expired
                'on_hold'          // Temporarily suspended
            ])->default('pending_start');
            
            // Performance Tracking
            $table->json('performance_reviews')->nullable(); // Monthly/quarterly reviews
            $table->decimal('performance_score', 5, 2)->nullable(); // Average performance score
            $table->integer('attendance_rate')->nullable(); // Percentage
            $table->text('supervisor_feedback')->nullable();
            
            // Renewal & Extension
            $table->boolean('is_renewable')->default(false);
            $table->boolean('renewal_offered')->default(false);
            $table->boolean('renewal_accepted')->default(false);
            $table->date('renewal_decision_deadline')->nullable();
            $table->text('renewal_notes')->nullable();
            
            // Contract Management
            $table->timestamp('contract_signed_at')->nullable();
            $table->string('contract_file_path')->nullable();
            $table->json('contract_amendments')->nullable(); // Any amendments made
            
            // Alerts & Notifications
            $table->boolean('expiry_alert_30_sent')->default(false);
            $table->boolean('expiry_alert_14_sent')->default(false);
            $table->boolean('expiry_alert_7_sent')->default(false);
            $table->timestamp('last_alert_sent_at')->nullable();
            
            // Termination Details
            $table->date('termination_date')->nullable();
            $table->enum('termination_reason', [
                'contract_completion',
                'mutual_agreement',
                'company_decision',
                'employee_resignation',
                'performance_issues',
                'misconduct',
                'force_majeure',
                'other'
            ])->nullable();
            $table->text('termination_notes')->nullable();
            $table->foreignId('terminated_by')->nullable()->constrained('users');
            
            // Commission & Agent Tracking
            $table->decimal('agent_commission', 10, 2)->default(0);
            $table->boolean('commission_paid')->default(false);
            $table->timestamp('commission_paid_at')->nullable();
            
            // Additional Information
            $table->text('placement_notes')->nullable();
            $table->json('documents')->nullable(); // Contract, amendments, etc.
            $table->foreignId('placed_by')->nullable()->constrained('users'); // HR who processed
            
            $table->timestamps();
            
            // Indexes for efficient querying
            $table->index(['status', 'start_date']);
            $table->index(['company_id', 'status']);
            $table->index(['agent_id', 'status']);
            $table->index(['end_date', 'status']); // For expiry alerts
            $table->index('placement_number');
            $table->index(['applicant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placements');
    }
};