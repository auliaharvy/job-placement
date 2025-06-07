<?php

namespace App\Jobs;

use App\Models\JobPosting;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BroadcastJobToApplicants implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60]; // Retry after 10s, 30s, 60s
    public $timeout = 300; // 5 minutes timeout

    protected JobPosting $jobPosting;

    /**
     * Create a new job instance.
     */
    public function __construct(JobPosting $jobPosting)
    {
        $this->jobPosting = $jobPosting;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Starting WhatsApp broadcast for job: {$this->jobPosting->title} (ID: {$this->jobPosting->id})");

            // Check if job is still valid for broadcasting
            if ($this->jobPosting->status !== 'published') {
                Log::warning("Job {$this->jobPosting->id} is no longer published, skipping broadcast");
                return;
            }

            if (!$this->jobPosting->auto_broadcast_whatsapp) {
                Log::warning("Auto broadcast disabled for job {$this->jobPosting->id}, skipping broadcast");
                return;
            }

            // Get WhatsApp service
            $whatsappService = app(WhatsAppService::class);

            // Find matching applicants
            $matchingApplicants = $this->jobPosting->findMatchingApplicants(100);
            
            if ($matchingApplicants->isEmpty()) {
                Log::info("No matching applicants found for job {$this->jobPosting->id}");
                return;
            }

            $broadcastId = 'JOB_' . $this->jobPosting->id . '_' . time();
            $successCount = 0;
            $failureCount = 0;

            Log::info("Found {$matchingApplicants->count()} matching applicants for job {$this->jobPosting->id}");

            foreach ($matchingApplicants as $applicant) {
                try {
                    // Skip if applicant doesn't have WhatsApp number
                    if (empty($applicant->whatsapp_number)) {
                        Log::warning("Applicant {$applicant->id} has no WhatsApp number, skipping");
                        continue;
                    }

                    // Generate message
                    $message = $this->generateJobBroadcastMessage($applicant);

                    // Send WhatsApp message
                    $result = $whatsappService->sendMessage(
                        $applicant->whatsapp_number,
                        $message,
                        'text',
                        [
                            'job_id' => $this->jobPosting->id,
                            'applicant_id' => $applicant->id,
                            'broadcast_id' => $broadcastId,
                            'message_type' => 'job_broadcast'
                        ]
                    );

                    if ($result['success']) {
                        $successCount++;
                        Log::info("WhatsApp sent successfully to applicant {$applicant->id} ({$applicant->whatsapp_number})");
                    } else {
                        $failureCount++;
                        Log::error("Failed to send WhatsApp to applicant {$applicant->id}: " . $result['message']);
                    }

                    // Add small delay between messages to avoid rate limiting
                    usleep(500000); // 0.5 second delay

                } catch (\Exception $e) {
                    $failureCount++;
                    Log::error("Exception sending WhatsApp to applicant {$applicant->id}: " . $e->getMessage());
                }
            }

            // Update broadcast statistics
            $this->jobPosting->increment('broadcast_count');
            $this->jobPosting->update(['last_broadcast_at' => now()]);

            Log::info("WhatsApp broadcast completed for job {$this->jobPosting->id}. Success: {$successCount}, Failed: {$failureCount}");

        } catch (\Exception $e) {
            Log::error("Failed to broadcast job {$this->jobPosting->id}: " . $e->getMessage());
            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Generate WhatsApp message for job broadcast
     */
    private function generateJobBroadcastMessage($applicant): string
    {
        $message = "🔔 *LOWONGAN KERJA BARU*\n\n";
        $message .= "Halo {$applicant->full_name}! 👋\n\n";
        $message .= "Kami memiliki kesempatan kerja yang cocok untuk Anda:\n\n";
        $message .= "🏢 *Perusahaan:* {$this->jobPosting->company->name}\n";
        $message .= "💼 *Posisi:* {$this->jobPosting->title}\n";
        $message .= "📍 *Lokasi:* {$this->jobPosting->work_city}, {$this->jobPosting->work_province}\n";
        $message .= "💰 *Gaji:* {$this->jobPosting->salary_range}\n";
        $message .= "📅 *Deadline:* " . $this->jobPosting->application_deadline->format('d M Y') . "\n\n";
        
        if ($this->jobPosting->isUrgent()) {
            $message .= "⚠️ *URGENT HIRING* ⚠️\n\n";
        }
        
        $message .= "Tertarik? Segera daftarkan diri Anda!\n\n";
        $message .= "Untuk melamar, silakan hubungi tim HR kami atau kunjungi kantor kami.\n\n";
        $message .= "Semoga beruntung! 🍀\n\n";
        $message .= "_Pesan ini dikirim otomatis oleh sistem Job Placement_";

        return $message;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job broadcast failed permanently for job {$this->jobPosting->id}: " . $exception->getMessage());
    }
}
