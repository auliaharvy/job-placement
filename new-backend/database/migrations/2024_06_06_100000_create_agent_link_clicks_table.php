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
        Schema::create('agent_link_clicks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id');
            $table->string('referral_code', 50)->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('session_id', 255)->nullable();
            $table->string('browser_fingerprint', 255)->nullable();
            $table->timestamp('clicked_at');
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index(['agent_id', 'clicked_at']);
            $table->index(['referral_code', 'clicked_at']);
            $table->index(['utm_source', 'clicked_at']);
            $table->index(['utm_medium', 'clicked_at']);
            $table->index(['utm_campaign', 'clicked_at']);
            $table->index('clicked_at');
            $table->index('converted_at');
            $table->index(['session_id', 'ip_address']); // For unique visitor tracking
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_link_clicks');
    }
};
