<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applicant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'agent_id',
        'nik',
        'birth_date',
        'birth_place',
        'gender',
        'religion',
        'marital_status',
        'height',
        'weight',
        'blood_type',
        'address',
        'city',
        'province',
        'postal_code',
        'whatsapp_number',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'education_level',
        'school_name',
        'major',
        'graduation_year',
        'gpa',
        'work_experience',
        'skills',
        'total_work_experience_months',
        'ktp_file',
        'ijazah_file',
        'cv_file',
        'photo_file',
        'certificate_files',
        'status',
        'work_status',
        'available_from',
        'preferred_positions',
        'preferred_locations',
        'expected_salary_min',
        'expected_salary_max',
        'registration_source',
        'profile_completed_at',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'work_experience' => 'array',
        'skills' => 'array',
        'certificate_files' => 'array',
        'preferred_positions' => 'array',
        'preferred_locations' => 'array',
        'expected_salary_min' => 'decimal:2',
        'expected_salary_max' => 'decimal:2',
        'available_from' => 'date',
        'profile_completed_at' => 'datetime',
        'gpa' => 'decimal:2',
    ];

    /**
     * Available education levels
     */
    const EDUCATION_LEVELS = [
        'sd' => 'SD',
        'smp' => 'SMP',
        'sma' => 'SMA',
        'smk' => 'SMK',
        'd1' => 'Diploma 1',
        'd2' => 'Diploma 2',
        'd3' => 'Diploma 3',
        's1' => 'Sarjana (S1)',
        's2' => 'Magister (S2)',
        's3' => 'Doktor (S3)',
    ];

    /**
     * Available statuses
     */
    const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'blacklisted' => 'Blacklisted',
    ];

    /**
     * Available work statuses
     */
    const WORK_STATUSES = [
        'available' => 'Available',
        'working' => 'Working',
        'not_available' => 'Not Available',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function placements(): HasMany
    {
        return $this->hasMany(Placement::class);
    }

    public function whatsappLogs(): HasMany
    {
        return $this->hasMany(WhatsAppLog::class);
    }

    /**
     * Accessors
     */
    public function getFullNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->full_name;
        }
        return $this->nik ?? 'Unknown';
    }

    public function getEmailAttribute(): string
    {
        return $this->user ? $this->user->email : '';
    }

    public function getAgeAttribute(): int
    {
        return $this->birth_date ? $this->birth_date->age : 0;
    }

    public function getWorkExperienceYearsAttribute(): float
    {
        return round(($this->total_work_experience_months ?? 0) / 12, 1);
    }

    /**
     * Helper Methods
     */
    public function isProfileCompleted(): bool
    {
        return !is_null($this->profile_completed_at);
    }

    public function isAvailable(): bool
    {
        return $this->work_status === 'available' && $this->status === 'active';
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('work_status', 'available')->where('status', 'active');
    }

    public function scopeEducationLevel($query, $level)
    {
        return $query->where('education_level', $level);
    }

    public function scopeCity($query, $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    public function scopeAgeRange($query, $minAge = null, $maxAge = null)
    {
        if ($minAge) {
            $maxDate = now()->subYears($minAge)->format('Y-m-d');
            $query->where('birth_date', '<=', $maxDate);
        }
        if ($maxAge) {
            $minDate = now()->subYears($maxAge)->format('Y-m-d');
            $query->where('birth_date', '>=', $minDate);
        }
        return $query;
    }

    public function scopeMinExperience($query, $months)
    {
        return $query->where('total_work_experience_months', '>=', $months);
    }

    public function scopeHasSkills($query, array $skills)
    {
        foreach ($skills as $skill) {
            $query->whereJsonContains('skills', $skill);
        }
        return $query;
    }

    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Fixed search scope for PostgreSQL compatibility
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->whereHas('user', function ($q) use ($search) {
            $q->whereRaw("CONCAT(first_name, ' ', last_name) ILIKE ?", ["%{$search}%"])
              ->orWhere('email', 'ILIKE', "%{$search}%");
        })->orWhere('nik', 'ILIKE', "%{$search}%")
          ->orWhere('whatsapp_number', 'ILIKE', "%{$search}%")
          ->orWhere('school_name', 'ILIKE', "%{$search}%")
          ->orWhere('major', 'ILIKE', "%{$search}%");
    }
}
