<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'created_by',
        'title',
        'position',
        'department',
        'employment_type',
        'description',
        'responsibilities',
        'requirements',
        'benefits',
        'work_location',
        'work_city',
        'work_province',
        'work_arrangement',
        'salary_min',
        'salary_max',
        'salary_negotiable',
        'contract_duration_months',
        'start_date',
        'application_deadline',
        'required_education_levels',
        'min_age',
        'max_age',
        'preferred_genders',
        'min_experience_months',
        'required_skills',
        'preferred_skills',
        'preferred_locations',
        'total_positions',
        'total_applications',
        'total_hired',
        'status',
        'published_at',
        'closed_at',
        'auto_broadcast_whatsapp',
        'last_broadcast_at',
        'broadcast_count',
        'priority',
        'is_featured',
        'internal_notes',
        'matching_algorithm_weights',
    ];

    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'salary_negotiable' => 'boolean',
        'start_date' => 'date',
        'application_deadline' => 'date',
        'required_education_levels' => 'array',
        'preferred_genders' => 'array',
        'required_skills' => 'array',
        'preferred_skills' => 'array',
        'preferred_locations' => 'array',
        'published_at' => 'datetime',
        'closed_at' => 'datetime',
        'auto_broadcast_whatsapp' => 'boolean',
        'last_broadcast_at' => 'datetime',
        'is_featured' => 'boolean',
        'matching_algorithm_weights' => 'array',
    ];

    /**
     * Available employment types
     */
    const EMPLOYMENT_TYPES = [
        'magang' => 'Magang (3-6 bulan)',
        'pkwt' => 'PKWT (12 bulan)',
        'project' => 'Project Based',
    ];

    /**
     * Available work arrangements
     */
    const WORK_ARRANGEMENTS = [
        'onsite' => 'On Site',
        'remote' => 'Remote',
        'hybrid' => 'Hybrid',
    ];

    /**
     * Available statuses
     */
    const STATUSES = [
        'draft' => 'Draft',
        'published' => 'Published',
        'paused' => 'Paused',
        'closed' => 'Closed',
        'cancelled' => 'Cancelled',
    ];

    /**
     * Available priorities
     */
    const PRIORITIES = [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    // Education level constants
    const EDUCATION_SD = 'sd';
    const EDUCATION_SMP = 'smp';
    const EDUCATION_SMA = 'sma';
    const EDUCATION_D3 = 'd3';
    const EDUCATION_S1 = 's1';
    const EDUCATION_S2 = 's2';

    // Gender requirement constants
    const GENDER_ANY = 'any';
    const GENDER_MALE_ONLY = 'male_only';
    const GENDER_FEMALE_ONLY = 'female_only';

    // Status constants
    const STATUS_ACTIVE = 'published';
    const STATUS_DRAFT = 'draft';
    const STATUS_PAUSED = 'paused';
    const STATUS_CLOSED = 'closed';

    /**
     * Relationships
     */

    /**
     * Get the company that owns this job posting
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created this job posting
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all applications for this job posting
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get all placements for this job posting
     */
    public function placements(): HasMany
    {
        return $this->hasMany(Placement::class);
    }

    /**
     * Get WhatsApp logs for this job posting
     */
    public function whatsappLogs(): HasMany
    {
        return $this->hasMany(WhatsAppLog::class);
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Get formatted salary range
     */
    public function getSalaryRangeAttribute(): string
    {
        if (!$this->salary_min && !$this->salary_max) {
            return 'Salary negotiable';
        }

        if ($this->salary_min && $this->salary_max) {
            return 'Rp ' . number_format($this->salary_min, 0, ',', '.') . ' - Rp ' . number_format($this->salary_max, 0, ',', '.');
        }

        if ($this->salary_min) {
            return 'Min Rp ' . number_format($this->salary_min, 0, ',', '.');
        }

        return 'Max Rp ' . number_format($this->salary_max, 0, ',', '.');
    }

    /**
     * Get days until application deadline
     */
    public function getDaysUntilDeadlineAttribute(): int
    {
        if (!$this->application_deadline) {
            return 0;
        }

        return max(0, now()->diffInDays($this->application_deadline, false));
    }

    /**
     * Check if job is still accepting applications
     */
    public function isAcceptingApplications(): bool
    {
        return $this->status === 'published' && 
               $this->application_deadline >= now()->toDateString() &&
               $this->total_hired < $this->total_positions;
    }

    /**
     * Check if job is urgent
     */
    public function isUrgent(): bool
    {
        return $this->priority === 'urgent';
    }

    /**
     * Check if job is featured
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Get application success rate
     */
    public function getSuccessRateAttribute(): float
    {
        if ($this->total_applications === 0) {
            return 0;
        }

        return round(($this->total_hired / $this->total_applications) * 100, 2);
    }

    /**
     * Get remaining positions
     */
    public function getRemainingPositionsAttribute(): int
    {
        return max(0, $this->total_positions - $this->total_hired);
    }

    /**
     * Find matching applicants for this job
     */
    public function findMatchingApplicants(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        $query = Applicant::available();

        // Filter by education level
        if (!empty($this->required_education_levels)) {
            $query->whereIn('education_level', $this->required_education_levels);
        }

        // Filter by age
        if ($this->min_age || $this->max_age) {
            $query->ageRange($this->min_age, $this->max_age);
        }

        // Filter by minimum experience
        if ($this->min_experience_months > 0) {
            $query->minExperience($this->min_experience_months);
        }

        // Filter by skills if specified
        if (!empty($this->required_skills)) {
            $query->hasSkills($this->required_skills);
        }

        // Filter by preferred locations
        if (!empty($this->preferred_locations)) {
            $query->where(function ($q) {
                foreach ($this->preferred_locations as $location) {
                    $q->orWhere('city', 'like', "%{$location}%")
                      ->orWhere('province', 'like', "%{$location}%");
                }
            });
        }

        // Filter by gender preference
        if (!empty($this->preferred_genders)) {
            $query->whereIn('gender', $this->preferred_genders);
        }

        // Exclude applicants who already applied to this job
        $query->whereDoesntHave('applications', function ($q) {
            $q->where('job_posting_id', $this->id);
        });

        $applicants = $query->limit($limit)->get();

        // Calculate matching scores
        foreach ($applicants as $applicant) {
            $matchResult = $applicant->matchesJobRequirements($this);
            $applicant->matching_score = $matchResult['score'];
            $applicant->matching_details = $matchResult['details'];
        }

        // Sort by matching score
        return $applicants->sortByDesc('matching_score');
    }

    /**
     * Broadcast job to matching applicants via WhatsApp
     */
    public function broadcastToMatchingApplicants(): array
    {
        if (!$this->auto_broadcast_whatsapp || $this->status !== 'published') {
            return ['success' => false, 'message' => 'Broadcast not enabled or job not published'];
        }

        $matchingApplicants = $this->findMatchingApplicants(100);
        $broadcastId = 'JOB_' . $this->id . '_' . time();
        $successCount = 0;
        $failureCount = 0;

        foreach ($matchingApplicants as $applicant) {
            try {
                // Create WhatsApp log entry
                WhatsAppLog::create([
                    'session_id' => 'main_session',
                    'phone_number' => $applicant->whatsapp_number,
                    'message_type' => 'template',
                    'message_content' => $this->generateJobBroadcastMessage($applicant),
                    'template_name' => 'job_opportunity',
                    'template_variables' => [
                        'applicant_name' => $applicant->full_name,
                        'job_title' => $this->title,
                        'company_name' => $this->company->name,
                        'location' => $this->work_city,
                        'salary_range' => $this->salary_range,
                    ],
                    'status' => 'pending',
                    'context_type' => 'job_broadcast',
                    'context_id' => $this->id,
                    'applicant_id' => $applicant->id,
                    'job_posting_id' => $this->id,
                    'broadcast_id' => $broadcastId,
                    'is_broadcast' => true,
                    'broadcast_sequence' => $successCount + 1,
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $failureCount++;
                \Log::error('Failed to create WhatsApp broadcast for applicant ' . $applicant->id . ': ' . $e->getMessage());
            }
        }

        // Update broadcast statistics
        $this->increment('broadcast_count');
        $this->update(['last_broadcast_at' => now()]);

        return [
            'success' => true,
            'broadcast_id' => $broadcastId,
            'total_sent' => $successCount,
            'total_failed' => $failureCount,
            'message' => "Broadcast sent to {$successCount} matching applicants"
        ];
    }

    /**
     * Generate WhatsApp message for job broadcast
     */
    private function generateJobBroadcastMessage(Applicant $applicant): string
    {
        $message = "🔔 *LOWONGAN KERJA BARU*\n\n";
        $message .= "Halo {$applicant->full_name}! 👋\n\n";
        $message .= "Kami memiliki kesempatan kerja yang cocok untuk Anda:\n\n";
        $message .= "🏢 *Perusahaan:* {$this->company->name}\n";
        $message .= "💼 *Posisi:* {$this->title}\n";
        $message .= "📍 *Lokasi:* {$this->work_city}, {$this->work_province}\n";
        $message .= "💰 *Gaji:* {$this->salary_range}\n";
        $message .= "📅 *Deadline:* " . $this->application_deadline->format('d M Y') . "\n\n";
        
        if ($this->isUrgent()) {
            $message .= "⚠️ *URGENT HIRING* ⚠️\n\n";
        }
        
        $message .= "Tertarik? Segera daftarkan diri Anda!\n\n";
        $message .= "Untuk melamar, silakan hubungi tim HR kami atau kunjungi kantor kami.\n\n";
        $message .= "Semoga beruntung! 🍀\n\n";
        $message .= "_Pesan ini dikirim otomatis oleh sistem_";

        return $message;
    }

    /**
     * Scopes
     */

    /**
     * Scope to get published jobs only
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to get active jobs (published and before deadline)
     */
    public function scopeActive($query)
    {
        return $query->published()
                    ->where('application_deadline', '>=', now()->toDateString());
    }

    /**
     * Scope to get featured jobs
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get urgent jobs
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    /**
     * Scope to filter by company
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to filter by employment type
     */
    public function scopeEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    /**
     * Scope to filter by city
     */
    public function scopeCity($query, $city)
    {
        return $query->where('work_city', 'like', "%{$city}%");
    }

    /**
     * Scope to filter by salary range
     */
    public function scopeSalaryRange($query, $minSalary = null, $maxSalary = null)
    {
        if ($minSalary) {
            $query->where('salary_max', '>=', $minSalary);
        }
        if ($maxSalary) {
            $query->where('salary_min', '<=', $maxSalary);
        }
        return $query;
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('position', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('work_city', 'like', "%{$search}%")
              ->orWhereHas('company', function ($companyQuery) use ($search) {
                  $companyQuery->where('name', 'like', "%{$search}%");
              });
        });
    }
}