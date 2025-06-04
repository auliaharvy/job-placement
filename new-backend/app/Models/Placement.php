<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Placement extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'applicant_id',
        'job_posting_id',
        'company_id',
        'agent_id',
        'placement_number',
        'employee_id',
        'position_title',
        'department',
        'work_location',
        'contract_type',
        'start_date',
        'end_date',
        'contract_duration_months',
        'salary',
        'benefits',
        'contract_terms',
        'status',
        'performance_reviews',
        'performance_score',
        'attendance_rate',
        'supervisor_feedback',
        'is_renewable',
        'renewal_offered',
        'renewal_accepted',
        'renewal_decision_deadline',
        'renewal_notes',
        'contract_signed_at',
        'contract_file_path',
        'contract_amendments',
        'expiry_alert_30_sent',
        'expiry_alert_14_sent',
        'expiry_alert_7_sent',
        'last_alert_sent_at',
        'termination_date',
        'termination_reason',
        'termination_notes',
        'terminated_by',
        'agent_commission',
        'commission_paid',
        'commission_paid_at',
        'placement_notes',
        'documents',
        'placed_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'salary' => 'decimal:2',
        'benefits' => 'array',
        'contract_terms' => 'array',
        'performance_reviews' => 'array',
        'performance_score' => 'decimal:2',
        'is_renewable' => 'boolean',
        'renewal_offered' => 'boolean',
        'renewal_accepted' => 'boolean',
        'renewal_decision_deadline' => 'date',
        'contract_signed_at' => 'datetime',
        'contract_amendments' => 'array',
        'expiry_alert_30_sent' => 'boolean',
        'expiry_alert_14_sent' => 'boolean',
        'expiry_alert_7_sent' => 'boolean',
        'last_alert_sent_at' => 'datetime',
        'termination_date' => 'date',
        'agent_commission' => 'decimal:2',
        'commission_paid' => 'boolean',
        'commission_paid_at' => 'datetime',
        'documents' => 'array',
    ];

    /**
     * Available contract types
     */
    const CONTRACT_TYPES = [
        'magang' => 'Magang (3-6 bulan)',
        'pkwt' => 'PKWT (12 bulan)',
        'project' => 'Project Based',
    ];

    /**
     * Available statuses
     */
    const STATUSES = [
        'pending_start' => 'Pending Start',
        'active' => 'Active',
        'completed' => 'Completed',
        'terminated' => 'Terminated',
        'expired' => 'Expired',
        'on_hold' => 'On Hold',
    ];

    /**
     * Available termination reasons
     */
    const TERMINATION_REASONS = [
        'contract_completion' => 'Contract Completion',
        'mutual_agreement' => 'Mutual Agreement',
        'company_decision' => 'Company Decision',
        'employee_resignation' => 'Employee Resignation',
        'performance_issues' => 'Performance Issues',
        'misconduct' => 'Misconduct',
        'force_majeure' => 'Force Majeure',
        'other' => 'Other',
    ];

    /**
     * Relationships
     */

    /**
     * Get the application that led to this placement
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the applicant for this placement
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Get the job posting for this placement
     */
    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * Get the company for this placement
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the agent who referred this placement
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the user who processed this placement
     */
    public function placedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'placed_by');
    }

    /**
     * Get the user who terminated this placement
     */
    public function terminatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'terminated_by');
    }

    /**
     * Accessors
     */

    /**
     * Get the placement's status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Get days until contract expiry
     */
    public function getDaysUntilExpiryAttribute(): int
    {
        if (!$this->end_date || $this->status !== 'active') {
            return 0;
        }

        return max(0, now()->diffInDays($this->end_date, false));
    }

    /**
     * Get contract duration in days
     */
    public function getContractDurationDaysAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Get days completed
     */
    public function getDaysCompletedAttribute(): int
    {
        if (!$this->start_date || $this->status === 'pending_start') {
            return 0;
        }

        $endDate = $this->status === 'active' ? now() : ($this->termination_date ?? $this->end_date);
        return $this->start_date->diffInDays($endDate);
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentageAttribute(): float
    {
        if ($this->contract_duration_days === 0) {
            return 0;
        }

        return min(100, round(($this->days_completed / $this->contract_duration_days) * 100, 2));
    }

    /**
     * Generate unique placement number
     */
    public static function generatePlacementNumber(): string
    {
        $year = date('Y');
        $lastPlacement = self::where('placement_number', 'like', "PLC-{$year}-%")
                            ->orderBy('placement_number', 'desc')
                            ->first();

        if ($lastPlacement) {
            $lastNumber = (int) substr($lastPlacement->placement_number, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "PLC-{$year}-" . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Check if contract is expiring soon
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->status === 'active' && 
               $this->end_date && 
               $this->days_until_expiry <= $days && 
               $this->days_until_expiry > 0;
    }

    /**
     * Send contract expiry alerts
     */
    public function sendExpiryAlerts(): void
    {
        $daysUntil = $this->days_until_expiry;

        // 30 days alert
        if ($daysUntil <= 30 && !$this->expiry_alert_30_sent) {
            $this->sendExpiryAlert(30);
            $this->update(['expiry_alert_30_sent' => true, 'last_alert_sent_at' => now()]);
        }

        // 14 days alert
        if ($daysUntil <= 14 && !$this->expiry_alert_14_sent) {
            $this->sendExpiryAlert(14);
            $this->update(['expiry_alert_14_sent' => true, 'last_alert_sent_at' => now()]);
        }

        // 7 days alert
        if ($daysUntil <= 7 && !$this->expiry_alert_7_sent) {
            $this->sendExpiryAlert(7);
            $this->update(['expiry_alert_7_sent' => true, 'last_alert_sent_at' => now()]);
        }
    }

    /**
     * Send expiry alert WhatsApp message
     */
    private function sendExpiryAlert(int $days): void
    {
        $message = $this->generateExpiryAlertMessage($days);
        
        WhatsAppLog::create([
            'session_id' => 'main_session',
            'phone_number' => $this->applicant->whatsapp_number,
            'message_type' => 'template',
            'message_content' => $message,
            'template_name' => 'contract_expiry_alert',
            'template_variables' => [
                'applicant_name' => $this->applicant->full_name,
                'company_name' => $this->company->name,
                'position' => $this->position_title,
                'days_until_expiry' => $days,
                'end_date' => $this->end_date->format('d M Y'),
            ],
            'status' => 'pending',
            'context_type' => 'contract_expiry',
            'context_id' => $this->id,
            'applicant_id' => $this->applicant_id,
        ]);
    }

    /**
     * Generate expiry alert message
     */
    private function generateExpiryAlertMessage(int $days): string
    {
        $applicantName = $this->applicant->full_name;
        $companyName = $this->company->name;
        $position = $this->position_title;
        $endDate = $this->end_date->format('d M Y');

        $message = "⚠️ *REMINDER KONTRAK KERJA* ⚠️\n\n";
        $message .= "Halo {$applicantName}!\n\n";
        $message .= "Kami ingin mengingatkan bahwa kontrak kerja Anda akan berakhir dalam *{$days} hari*.\n\n";
        $message .= "📋 *Detail Kontrak:*\n";
        $message .= "🏢 Perusahaan: {$companyName}\n";
        $message .= "💼 Posisi: {$position}\n";
        $message .= "📅 Berakhir: {$endDate}\n";
        $message .= "📞 Placement No: {$this->placement_number}\n\n";
        
        if ($this->is_renewable) {
            $message .= "💡 *Informasi Perpanjangan:*\n";
            $message .= "Kontrak Anda dapat diperpanjang. Silakan hubungi tim HR untuk informasi lebih lanjut.\n\n";
        }
        
        $message .= "Harap segera koordinasikan dengan atasan Anda mengenai:";
        $message .= "\n• Penyelesaian pekerjaan yang sedang berjalan";
        $message .= "\n• Serah terima tugas dan tanggung jawab";
        $message .= "\n• Proses perpanjangan kontrak (jika tersedia)";
        $message .= "\n• Exit interview dan proses offboarding\n\n";
        $message .= "Jika ada pertanyaan, silakan hubungi tim HR.\n\n";
        $message .= "Terima kasih atas dedikasi Anda selama ini! 🙏\n\n";
        $message .= "_Pesan otomatis dari sistem placement_";

        return $message;
    }

    /**
     * Terminate placement
     */
    public function terminate(string $reason, ?string $notes = null, ?int $terminatedBy = null): bool
    {
        $this->update([
            'status' => 'terminated',
            'termination_date' => now()->toDateString(),
            'termination_reason' => $reason,
            'termination_notes' => $notes,
            'terminated_by' => $terminatedBy,
        ]);

        // Update applicant work status
        $this->applicant->update(['work_status' => 'available']);

        return true;
    }

    /**
     * Complete placement (natural end)
     */
    public function complete(): bool
    {
        $this->update([
            'status' => 'completed',
        ]);

        // Update applicant work status
        $this->applicant->update(['work_status' => 'available']);

        return true;
    }

    /**
     * Calculate and process agent commission
     */
    public function processAgentCommission(): void
    {
        if (!$this->agent || $this->commission_paid) {
            return;
        }

        $commissionAmount = $this->agent->calculateCommission($this->salary);
        
        $this->update([
            'agent_commission' => $commissionAmount,
            'commission_paid' => true,
            'commission_paid_at' => now(),
        ]);

        // Add commission to agent
        $this->agent->addCommission($commissionAmount, $this->id);
    }

    /**
     * Scopes
     */

    /**
     * Scope to get active placements
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get expiring placements
     */
    public function scopeExpiring($query, $days = 30)
    {
        return $query->active()
                    ->whereNotNull('end_date')
                    ->where('end_date', '<=', now()->addDays($days))
                    ->where('end_date', '>', now());
    }

    /**
     * Scope to filter by company
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to filter by agent
     */
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope to filter by contract type
     */
    public function scopeContractType($query, $type)
    {
        return $query->where('contract_type', $type);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('placement_number', 'like', "%{$search}%")
              ->orWhere('employee_id', 'like', "%{$search}%")
              ->orWhere('position_title', 'like', "%{$search}%")
              ->orWhereHas('applicant.user', function ($userQuery) use ($search) {
                  $userQuery->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%");
              })
              ->orWhereHas('company', function ($companyQuery) use ($search) {
                  $companyQuery->where('name', 'like', "%{$search}%");
              });
        });
    }
}