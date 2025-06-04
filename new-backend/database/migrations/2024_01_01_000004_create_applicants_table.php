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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_id')->nullable()->constrained()->onDelete('set null'); // Referral agent
            
            // Personal Information
            $table->string('nik', 16)->unique();
            $table->date('birth_date');
            $table->string('birth_place');
            $table->enum('gender', ['male', 'female']);
            $table->string('religion')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->default('single');
            $table->integer('height')->nullable(); // in cm
            $table->integer('weight')->nullable(); // in kg
            $table->string('blood_type')->nullable();
            
            // Contact Information
            $table->text('address');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code')->nullable();
            $table->string('whatsapp_number');
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            
            // Education
            $table->enum('education_level', ['sd', 'smp', 'sma', 'smk', 'd1', 'd2', 'd3', 's1', 's2', 's3']);
            $table->string('school_name');
            $table->string('major')->nullable();
            $table->year('graduation_year');
            $table->decimal('gpa', 3, 2)->nullable();
            
            // Work Experience
            $table->text('work_experience')->nullable(); // JSON format for multiple experiences
            $table->text('skills')->nullable(); // JSON format for skills list
            $table->integer('total_work_experience_months')->default(0);
            
            // Documents
            $table->string('ktp_file')->nullable();
            $table->string('ijazah_file')->nullable();
            $table->string('cv_file')->nullable();
            $table->string('photo_file')->nullable();
            $table->json('certificate_files')->nullable(); // Array of certificate file paths
            
            // Status & Availability
            $table->enum('status', ['active', 'inactive', 'blacklisted'])->default('active');
            $table->enum('work_status', ['available', 'working', 'not_available'])->default('available');
            $table->date('available_from')->nullable();
            $table->text('preferred_positions')->nullable(); // JSON array
            $table->text('preferred_locations')->nullable(); // JSON array
            $table->decimal('expected_salary_min', 12, 2)->nullable();
            $table->decimal('expected_salary_max', 12, 2)->nullable();
            
            // Registration Info
            $table->string('registration_source')->nullable(); // QR code, direct, referral, etc.
            $table->timestamp('profile_completed_at')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes for search and filtering
            $table->index(['status', 'work_status']);
            $table->index(['education_level', 'graduation_year']);
            $table->index(['city', 'province']);
            $table->index('agent_id');
            $table->index('nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};