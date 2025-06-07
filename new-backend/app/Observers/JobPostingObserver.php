<?php

namespace App\Observers;

use App\Models\JobPosting;
use App\Jobs\BroadcastJobToApplicants;
use Illuminate\Support\Facades\Log;

class JobPostingObserver
{
    /**
     * Handle the JobPosting "created" event.
     */
    public function created(JobPosting $jobPosting): void
    {
        // Only broadcast if job is published and auto broadcast is enabled
        if ($jobPosting->status === 'published' && $jobPosting->auto_broadcast_whatsapp) {
            try {
                // Dispatch job broadcast in background
                BroadcastJobToApplicants::dispatch($jobPosting)
                    ->delay(now()->addSeconds(30)); // Add 30 second delay to ensure job is fully saved
                
                Log::info("Queued WhatsApp broadcast for job: {$jobPosting->title} (ID: {$jobPosting->id})");
            } catch (\Exception $e) {
                Log::error("Failed to queue WhatsApp broadcast for job {$jobPosting->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Handle the JobPosting "updated" event.
     */
    public function updated(JobPosting $jobPosting): void
    {
        // Check if job was just published (status changed from draft to published)
        if ($jobPosting->isDirty('status') && 
            $jobPosting->status === 'published' && 
            $jobPosting->getOriginal('status') !== 'published' &&
            $jobPosting->auto_broadcast_whatsapp) {
            
            try {
                // Dispatch job broadcast in background
                BroadcastJobToApplicants::dispatch($jobPosting)
                    ->delay(now()->addSeconds(30));
                
                Log::info("Queued WhatsApp broadcast for newly published job: {$jobPosting->title} (ID: {$jobPosting->id})");
            } catch (\Exception $e) {
                Log::error("Failed to queue WhatsApp broadcast for job {$jobPosting->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Handle the JobPosting "deleted" event.
     */
    public function deleted(JobPosting $jobPosting): void
    {
        // Log when job is deleted
        Log::info("Job posting deleted: {$jobPosting->title} (ID: {$jobPosting->id})");
    }
}
