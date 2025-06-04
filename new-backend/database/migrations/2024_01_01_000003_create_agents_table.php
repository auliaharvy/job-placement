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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('agent_code')->unique(); // Format: AGT001, AGT002, etc.
            $table->string('referral_code', 10)->unique(); // Short code for QR generation
            $table->integer('total_referrals')->default(0);
            $table->integer('successful_placements')->default(0);
            $table->decimal('total_commission', 15, 2)->default(0);
            $table->integer('total_points')->default(0);
            $table->enum('level', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');
            $table->decimal('success_rate', 5, 2)->default(0); // Percentage
            $table->json('performance_metrics')->nullable(); // Monthly stats, rankings, etc.
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'level']);
            $table->index('agent_code');
            $table->index('referral_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};