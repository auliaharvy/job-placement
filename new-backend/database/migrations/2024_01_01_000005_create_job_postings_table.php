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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // HR Staff who created
            
            // Job Basic Info
            $table->string('title');
            $table->string('position');
            $table->string('department')->nullable();
            $table->enum('employment_type', ['magang', 'pkwt', 'project']); // Internship, Contract, Project
            $table->text('description');
            $table->text('responsibilities')->nullable();
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            
            // Job Details
            $table->string('work_location');
            $table->string('work_city');
            $table->string('work_province');
            $table->enum('work_arrangement', ['onsite', 'remote', 'hybrid'])->default('onsite');
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->boolean('salary_negotiable')->default(false);
            
            // Contract Details
            $table->integer('contract_duration_months')->nullable(); // For PKWT
            $table->date('start_date')->nullable();
            $table->date('application_deadline');
            
            // Matching Criteria
            $table->json('required_education_levels'); // Array: ['sma', 'smk', 'd3', 's1']
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
            $table->json('preferred_genders')->nullable(); // Array: ['male', 'female'] or null for any
            $table->integer('min_experience_months')->default(0);
            $table->json('required_skills')->nullable(); // Array of required skills
            $table->json('preferred_skills')->nullable(); // Array of preferred skills
            $table->json('preferred_locations')->nullable(); // Array of preferred applicant locations
            
            // Quota & Applications
            $table->integer('total_positions')->default(1);
            $table->integer('total_applications')->default(0);
            $table->integer('total_hired')->default(0);
            
            // Status & Publishing
            $table->enum('status', ['draft', 'published', 'paused', 'closed', 'cancelled'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->boolean('auto_broadcast_whatsapp')->default(true);
            $table->timestamp('last_broadcast_at')->nullable();
            $table->integer('broadcast_count')->default(0);
            
            // Priority & Urgency
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->boolean('is_featured')->default(false);
            
            // Additional Info
            $table->text('internal_notes')->nullable(); // Notes for internal use only
            $table->json('matching_algorithm_weights')->nullable(); // Custom weights for matching
            
            $table->timestamps();
            
            // Indexes for efficient querying
            $table->index(['status', 'published_at']);
            $table->index(['work_city', 'work_province']);
            $table->index(['employment_type', 'priority']);
            $table->index('company_id');
            $table->index('application_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};