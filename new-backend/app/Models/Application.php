<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'job_posting_id',
        'agent_id',
        'application_number',
        'source',
        'applied_at',
        'current_stage',
        'status',
        'matching_score',
        'matching_details',
        'selection_process',
        // Screening stage
        'screening_scheduled_at',
        'screening_completed_at',
        'screening_result',
        'screening_notes',
        'screened_by',
        // Psikotes stage
        'psikotes_scheduled_at',
        'psikotes_completed_at',
        'psikotes_result',
        'psikotes_score',
        'psikotes_notes',
        'psikotes_location',
        // Interview stage
        'interview_scheduled_at',
        'interview_completed_at',
        'interview_result',
        'interview_score',
        'interview_notes',
        'interviewed_by',
        'interview_location',
        'interview_type',
        // Medical stage
        'medical_scheduled_at',
        'medical_completed_at',
        'medical_result',
        'medical_notes',
        'medical_location',
        'medical_details',
        // Final decision
        'final_decision_at',
        'final_decision_by',
        'final_decision_notes',
        'rejection_reason',
        // Additional info
        'notification_log',
        'last_notification_sent_at',
        'applicant_notes',
        'internal_notes',
        'documents_submitted',
        'documents_verified',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'matching_score' => 'decimal:2',
        'matching_details' => 'array',
        'selection_process' => 'array',
        'screening_scheduled_at' => 'datetime',
        'screening_completed_at' => 'datetime',
        'psikotes_scheduled_at' => 'datetime',
        'psikotes_completed_at' => 'datetime',
        'psikotes_score' => 'decimal:2',
        'interview_scheduled_at' => 'datetime',
        'interview_completed_at' => 'datetime',
        'interview_score' => 'decimal:2',
        'medical_scheduled_at' => 'datetime',
        'medical_completed_at' => 'datetime',
        'medical_details' => 'array',
        'final_decision_at' => 'datetime',
        'notification_log' => 'array',
        'last_notification_sent_at' => 'datetime',
        'documents_submitted' => 'array',
        'documents_verified' => 'boolean',
    ];

    /**
     * Available selection stages
     */
    const STAGES = [
        'applied' => 'Applied',
        'screening' => 'Screening',
        'psikotes' => 'Psikotes',
        'interview' => 'Interview',
        'medical' => 'Medical Checkup',
        'final_review' => 'Final Review',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
    ];

    /**
     * Available statuses
     */
    const STATUSES = [
        'active' => 'Active',
        'withdrawn' => 'Withdrawn',
        'rejected' => 'Rejected',
        'accepted' => 'Accepted',
        'placed' => 'Placed',
    ];

    /**
     * Available sources
     */
    const SOURCES = [
        'direct' => 'Direct Application',
        'agent_referral' => 'Agent Referral',
        'whatsapp_broadcast' => 'WhatsApp Broadcast',
        'walk_in' => 'Walk In',
    ];

    // Stage constants
    const STAGE_APPLICATION = 'applied';
    const STAGE_SCREENING = 'screening';
    const STAGE_PSYCOTEST = 'psikotes';
    const STAGE_INTERVIEW = 'interview';
    const STAGE_MEDICAL = 'medical';
    const STAGE_FINAL = 'final_review';
    const STAGE_ACCEPTED = 'accepted';
    const STAGE_REJECTED = 'rejected';

    /**
     * Stage results
     */
    const STAGE_RESULTS = [
        'pass' => 'Pass',
        'fail' => 'Fail',
        'pending' => 'Pending',
    ];

    /**
     * Interview types
     */
    const INTERVIEW_TYPES = [
        'online' => 'Online',
        'offline' => 'Offline',
    ];

    /**
     * Relationships
     */

    /**
     * Get the applicant that owns this application
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Get the job posting for this application
     */
    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * Get the agent who referred this application
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the user who screened this application
     */
    public function screener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'screened_by');
    }

    /**
     * Get the user who interviewed this application
     */
    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewed_by');
    }

    /**
     * Get the user who made the final decision
     */
    public function finalDecisionMaker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'final_decision_by');
    }

    /**
     * Get the placement if application is accepted
     */
    public function placement(): HasOne
    {
        return $this->hasOne(Placement::class);
    }

    /**
     * Get WhatsApp logs for this application
     */
    public function whatsappLogs(): HasMany
    {
        return $this->hasMany(WhatsAppLog::class);
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Get the current stage display name
     */
    public function getCurrentStageDisplayAttribute(): string
    {
        return self::STAGES[$this->current_stage] ?? $this->current_stage;
    }

    /**
     * Get the status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Get the source display name
     */
    public function getSourceDisplayAttribute(): string
    {
        return self::SOURCES[$this->source] ?? $this->source;
    }

    /**
     * Get days since application
     */
    public function getDaysSinceApplicationAttribute(): int
    {
        return $this->applied_at ? $this->applied_at->diffInDays(now()) : 0;
    }

    /**
     * Get overall score (average of all completed stages)
     */
    public function getOverallScoreAttribute(): float
    {
        $scores = [];
        
        if ($this->psikotes_score) {
            $scores[] = $this->psikotes_score;
        }
        
        if ($this->interview_score) {
            $scores[] = $this->interview_score;
        }

        return count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0;
    }

    /**
     * Generate unique application number
     */
    public static function generateApplicationNumber(): string
    {
        $year = date('Y');
        $lastApplication = self::where('application_number', 'like', "APP-{$year}-%")
                              ->orderBy('application_number', 'desc')
                              ->first();

        if ($lastApplication) {
            $lastNumber = (int) substr($lastApplication->application_number, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "APP-{$year}-" . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Check if application can proceed to next stage
     */
    public function canProceedToNextStage(): bool
    {
        switch ($this->current_stage) {
            case 'applied':
                return true; // Can always start screening
            case 'screening':
                return $this->screening_result === 'pass';
            case 'psikotes':
                return $this->psikotes_result === 'pass';
            case 'interview':
                return $this->interview_result === 'pass';
            case 'medical':
                return $this->medical_result === 'pass';
            case 'final_review':
                return false; // Final decision needed
            default:
                return false;
        }
    }

    /**
     * Get next stage
     */
    public function getNextStage(): ?string
    {
        $stages = ['applied', 'screening', 'psikotes', 'interview', 'medical', 'final_review'];
        $currentIndex = array_search($this->current_stage, $stages);
        
        if ($currentIndex !== false && $currentIndex < count($stages) - 1) {
            return $stages[$currentIndex + 1];
        }
        
        return null;
    }

    /**
     * Progress to next stage
     */
    public function progressToNextStage(): bool
    {
        if (!$this->canProceedToNextStage()) {
            return false;
        }

        $nextStage = $this->getNextStage();
        if (!$nextStage) {
            return false;
        }

        $this->update(['current_stage' => $nextStage]);
        
        // Send notification about stage progression
        $this->sendStageProgressionNotification($nextStage);
        
        return true;
    }

    /**
     * Reject application at current stage
     */
    public function reject(string $reason, ?int $rejectedBy = null): bool
    {
        $this->update([
            'current_stage' => 'rejected',
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'final_decision_at' => now(),
            'final_decision_by' => $rejectedBy,
        ]);

        // Send rejection notification
        $this->sendRejectionNotification($reason);
        
        return true;
    }

    /**
     * Accept application (final stage)
     */
    public function accept(?int $acceptedBy = null, ?string $notes = null): bool
    {
        $this->update([
            'current_stage' => 'accepted',
            'status' => 'accepted',
            'final_decision_at' => now(),
            'final_decision_by' => $acceptedBy,
            'final_decision_notes' => $notes,
        ]);

        // Send acceptance notification
        $this->sendAcceptanceNotification();
        
        return true;
    }

    /**
     * Send stage progression notification via WhatsApp
     */
    public function sendStageProgressionNotification(string $stage): void
    {
        $message = $this->generateStageProgressionMessage($stage);
        
        WhatsAppLog::create([
            'session_id' => 'main_session',
            'phone_number' => $this->applicant->whatsapp_number,
            'message_type' => 'template',
            'message_content' => $message,
            'template_name' => 'selection_update',
            'status' => 'pending',
            'context_type' => 'selection_update',
            'context_id' => $this->id,
            'applicant_id' => $this->applicant_id,
            'job_posting_id' => $this->job_posting_id,
            'application_id' => $this->id,
        ]);

        $this->update(['last_notification_sent_at' => now()]);
    }

    /**
     * Send rejection notification
     */
    public function sendRejectionNotification(string $reason): void
    {
        $message = $this->generateRejectionMessage($reason);
        
        WhatsAppLog::create([
            'session_id' => 'main_session',
            'phone_number' => $this->applicant->whatsapp_number,
            'message_type' => 'template',
            'message_content' => $message,
            'template_name' => 'application_rejected',
            'status' => 'pending',
            'context_type' => 'application_rejected',
            'context_id' => $this->id,
            'applicant_id' => $this->applicant_id,
            'job_posting_id' => $this->job_posting_id,
            'application_id' => $this->id,
        ]);
    }

    /**
     * Send acceptance notification
     */
    public function sendAcceptanceNotification(): void
    {
        $message = $this->generateAcceptanceMessage();
        
        WhatsAppLog::create([
            'session_id' => 'main_session',
            'phone_number' => $this->applicant->whatsapp_number,
            'message_type' => 'template',
            'message_content' => $message,
            'template_name' => 'application_accepted',
            'status' => 'pending',
            'context_type' => 'application_accepted',
            'context_id' => $this->id,
            'applicant_id' => $this->applicant_id,
            'job_posting_id' => $this->job_posting_id,
            'application_id' => $this->id,
        ]);
    }

    /**
     * Generate stage progression WhatsApp message
     */
    private function generateStageProgressionMessage(string $stage): string
    {
        $stageName = self::STAGES[$stage] ?? $stage;
        $applicantName = $this->applicant->full_name;
        $jobTitle = $this->jobPosting->title;
        $companyName = $this->jobPosting->company->name;

        $message = "🎉 *UPDATE LAMARAN ANDA*\n\n";
        $message .= "Halo {$applicantName}!\n\n";
        $message .= "Kami dengan senang hati menginformasikan bahwa lamaran Anda untuk posisi *{$jobTitle}* di {$companyName} telah berhasil maju ke tahap selanjutnya.\n\n";
        $message .= "📋 *Tahap Saat Ini:* {$stageName}\n";
        $message .= "📞 *Nomor Aplikasi:* {$this->application_number}\n\n";

        switch ($stage) {
            case 'screening':
                $message .= "Tahap selanjutnya adalah *Screening Awal*. Tim HR akan menghubungi Anda dalam 1-2 hari kerja untuk proses screening.\n\n";
                break;
            case 'psikotes':
                $message .= "Tahap selanjutnya adalah *Psikotes*. Anda akan dijadwalkan untuk mengikuti tes psikologi. Informasi lebih lanjut akan diberikan segera.\n\n";
                break;
            case 'interview':
                $message .= "Selamat! Anda lolos ke tahap *Interview*. Tim HR akan menghubungi Anda untuk penjadwalan interview.\n\n";
                break;
            case 'medical':
                $message .= "Tahap selanjutnya adalah *Medical Checkup*. Anda akan dijadwalkan untuk pemeriksaan kesehatan.\n\n";
                break;
            case 'final_review':
                $message .= "Lamaran Anda sedang dalam tahap *Final Review*. Keputusan akhir akan diinformasikan dalam waktu dekat.\n\n";
                break;
        }

        $message .= "Pastikan nomor telepon Anda selalu aktif agar kami dapat menghubungi Anda.\n\n";
        $message .= "Terima kasih atas kesabaran Anda. 🙏\n\n";
        $message .= "_Pesan otomatis dari sistem recruitment_";

        return $message;
    }

    /**
     * Generate rejection WhatsApp message
     */
    private function generateRejectionMessage(string $reason): string
    {
        $applicantName = $this->applicant->full_name;
        $jobTitle = $this->jobPosting->title;
        $companyName = $this->jobPosting->company->name;

        $message = "📝 *UPDATE LAMARAN ANDA*\n\n";
        $message .= "Halo {$applicantName},\n\n";
        $message .= "Terima kasih atas minat Anda untuk bergabung dengan {$companyName} sebagai {$jobTitle}.\n\n";
        $message .= "Setelah melalui proses seleksi yang ketat, dengan berat hati kami informasikan bahwa lamaran Anda belum dapat kami terima pada kesempatan ini.\n\n";
        $message .= "📞 *Nomor Aplikasi:* {$this->application_number}\n";
        $message .= "🏢 *Posisi:* {$jobTitle}\n";
        $message .= "🗓️ *Tanggal Keputusan:* " . now()->format('d M Y') . "\n\n";
        
        if ($reason && $reason !== 'general') {
            $message .= "💭 *Catatan:* {$reason}\n\n";
        }
        
        $message .= "Jangan berkecil hati! Kami akan menghubungi Anda jika ada posisi lain yang sesuai dengan profil Anda.\n\n";
        $message .= "Tetap semangat dan sukses selalu! 💪\n\n";
        $message .= "_Pesan otomatis dari sistem recruitment_";

        return $message;
    }

    /**
     * Generate acceptance WhatsApp message
     */
    private function generateAcceptanceMessage(): string
    {
        $applicantName = $this->applicant->full_name;
        $jobTitle = $this->jobPosting->title;
        $companyName = $this->jobPosting->company->name;

        $message = "🎉 *SELAMAT! ANDA DITERIMA* 🎉\n\n";
        $message .= "Halo {$applicantName}!\n\n";
        $message .= "Kami dengan bangga menginformasikan bahwa Anda *DITERIMA* untuk bergabung dengan {$companyName}!\n\n";
        $message .= "🎯 *Posisi:* {$jobTitle}\n";
        $message .= "🏢 *Perusahaan:* {$companyName}\n";
        $message .= "📞 *Nomor Aplikasi:* {$this->application_number}\n";
        $message .= "🗓️ *Tanggal Keputusan:* " . now()->format('d M Y') . "\n\n";
        $message .= "Tim HR akan segera menghubungi Anda untuk proses selanjutnya termasuk:\n";
        $message .= "• Penyiapan kontrak kerja\n";
        $message .= "• Orientasi dan onboarding\n";
        $message .= "• Informasi mulai kerja\n\n";
        $message .= "Pastikan nomor telepon Anda selalu aktif.\n\n";
        $message .= "Selamat bergabung dengan keluarga besar {$companyName}! 🚀\n\n";
        $message .= "_Pesan otomatis dari sistem recruitment_";

        return $message;
    }

    /**
     * Scopes
     */

    /**
     * Scope to get active applications
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by stage
     */
    public function scopeStage($query, $stage)
    {
        return $query->where('current_stage', $stage);
    }

    /**
     * Scope to filter by job posting
     */
    public function scopeForJob($query, $jobId)
    {
        return $query->where('job_posting_id', $jobId);
    }

    /**
     * Scope to filter by applicant
     */
    public function scopeForApplicant($query, $applicantId)
    {
        return $query->where('applicant_id', $applicantId);
    }

    /**
     * Scope to filter by agent
     */
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope to get applications within date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('applied_at', [$startDate, $endDate]);
    }

    /**
     * Scope to search applications
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('application_number', 'like', "%{$search}%")
              ->orWhereHas('applicant.user', function ($userQuery) use ($search) {
                  $userQuery->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
              })
              ->orWhereHas('jobPosting', function ($jobQuery) use ($search) {
                  $jobQuery->where('title', 'like', "%{$search}%")
                          ->orWhere('position', 'like', "%{$search}%");
              });
        });
    }
}