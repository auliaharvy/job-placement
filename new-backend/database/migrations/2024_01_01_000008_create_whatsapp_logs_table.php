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
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            
            // Message Basic Info
            $table->string('message_id')->nullable(); // WhatsApp message ID
            $table->string('session_id'); // WhatsApp session identifier
            $table->string('phone_number'); // Target phone number
            $table->enum('message_type', ['text', 'image', 'document', 'template', 'location']);
            
            // Message Content
            $table->text('message_content'); // The actual message content
            $table->string('template_name')->nullable(); // If using template
            $table->json('template_variables')->nullable(); // Template variable values
            $table->string('media_file_path')->nullable(); // For images/documents
            $table->string('media_caption')->nullable();
            
            // Delivery Tracking
            $table->enum('status', [
                'pending',      // Queued for sending
                'sent',         // Successfully sent
                'delivered',    // Delivered to recipient
                'read',         // Read by recipient
                'failed',       // Failed to send
                'expired'       // Message expired
            ])->default('pending');
            
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            
            // Error Handling
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('next_retry_at')->nullable();
            
            // Context & Tracking
            $table->string('context_type')->nullable(); // job_broadcast, selection_update, etc.
            $table->unsignedBigInteger('context_id')->nullable(); // Related job_posting_id, application_id, etc.
            $table->foreignId('triggered_by')->nullable()->constrained('users'); // User who triggered the message
            $table->foreignId('applicant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('job_posting_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('application_id')->nullable()->constrained()->onDelete('cascade');
            
            // Broadcast Info (for bulk messages)
            $table->string('broadcast_id')->nullable(); // Group related messages
            $table->boolean('is_broadcast')->default(false);
            $table->integer('broadcast_sequence')->nullable(); // Order in broadcast
            
            // Additional Metadata
            $table->json('metadata')->nullable(); // Additional context data
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes for efficient querying
            $table->index(['phone_number', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['context_type', 'context_id']);
            $table->index('broadcast_id');
            $table->index(['applicant_id', 'created_at']);
            $table->index(['job_posting_id', 'created_at']);
            $table->index(['session_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};