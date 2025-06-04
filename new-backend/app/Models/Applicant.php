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

    // Constants for status values
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_BLACKLISTED = 'blacklisted';

    // Constants for availability status
    const AVAILABILITY_AVAILABLE = 'available';
    const AVAILABILITY_WORKING = 'working';
    const AVAILABILITY_NOT_AVAILABLE = 'not_available';

    // Constants for gender
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    /**
     * Relationships
     */

    /**
     * Get the user that owns the applicant profile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the agent who referred this applicant
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get all applications for this applicant
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get all placements for this applicant
     */
    public function placements(): HasMany
    {
        return $this->hasMany(Placement::class);
    }

    /**
     * Get WhatsApp logs for this applicant
     */
    public function whatsappLogs(): HasMany
    {
        return $this->hasMany(WhatsAppLog::class);
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Get the applicant's full name from user relationship
     */
    public function getFullNameAttribute(): string
    {
        if ($this->user && $this->user->name) {
            return $this->user->name;
        }
        
        // Fallback to NIK if user data not available
        return $this->nik ?? 'Unknown';
    }

    /**
     * Get the applicant's email from user relationship
     */
    public function getEmailAttribute(): string
    {
        return $this->user && $this->user->email ? $this->user->email : '';
    }

    /**
     * Get the applicant's age
     */
    public function getAgeAttribute(): int
    {
        return $this->birth_date ? $this->birth_date->age : 0;
    }

    /**
     * Get work experience in years
     */
    public function getWorkExperienceYearsAttribute(): float
    {
        return round($this->total_work_experience_months / 12, 1);
    }

    /**
     * Check if profile is completed
     */
    public function isProfileCompleted(): bool
    {
        return !is_null($this->profile_completed_at);
    }

    /**
     * Check if applicant is available for work
     */
    public function isAvailable(): bool
    {
        return $this->work_status === 'available' && $this->status === 'active';
    }

    /**
     * Get technical skills only
     */
    public function getTechnicalSkillsAttribute(): array
    {
        $skills = $this->skills ?? [];
        return $skills['technical'] ?? [];
    }

    /**
     * Get soft skills only
     */
    public function getSoftSkillsAttribute(): array
    {
        $skills = $this->skills ?? [];
        return $skills['soft_skills'] ?? [];
    }

    /**
     * Check if applicant has specific skill
     */
    public function hasSkill(string $skill): bool
    {
        $allSkills = array_merge($this->technical_skills, $this->soft_skills);
        return in_array($skill, $allSkills);
    }

    /**
     * Check if applicant matches job requirements
     */
    public function matchesJobRequirements(JobPosting $job): array
    {
        $score = 0;
        $maxScore = 0;
        $details = [];

        // Education Level Match
        $maxScore += 20;
        if (in_array($this->education_level, $job->required_education_levels ?? [])) {
            $score += 20;
            $details['education'] = ['match' => true, 'score' => 20];
        } else {
            $details['education'] = ['match' => false, 'score' => 0];
        }

        // Age Match
        $maxScore += 10;
        $age = $this->age;
        if ((!$job->min_age || $age >= $job->min_age) && (!$job->max_age || $age <= $job->max_age)) {
            $score += 10;
            $details['age'] = ['match' => true, 'score' => 10];
        } else {
            $details['age'] = ['match' => false, 'score' => 0];
        }

        // Experience Match
        $maxScore += 25;
        if ($this->total_work_experience_months >= $job->min_experience_months) {
            $score += 25;
            $details['experience'] = ['match' => true, 'score' => 25];
        } else {
            $experienceScore = ($this->total_work_experience_months / $job->min_experience_months) * 25;
            $score += min($experienceScore, 25);
            $details['experience'] = ['match' => false, 'score' => min($experienceScore, 25)];
        }

        // Skills Match
        $maxScore += 30;
        $requiredSkills = $job->required_skills ?? [];
        $applicantSkills = array_merge($this->technical_skills, $this->soft_skills);
        $matchingSkills = array_intersect($requiredSkills, $applicantSkills);
        $skillScore = count($requiredSkills) > 0 ? (count($matchingSkills) / count($requiredSkills)) * 30 : 30;
        $score += $skillScore;
        $details['skills'] = [
            'match' => count($matchingSkills) === count($requiredSkills),
            'score' => $skillScore,
            'matching_skills' => $matchingSkills,
            'required_skills' => $requiredSkills
        ];

        // Location Match
        $maxScore += 15;
        $preferredLocations = $job->preferred_locations ?? [];
        if (empty($preferredLocations) || in_array($this->city, $preferredLocations) || in_array($this->province, $preferredLocations)) {
            $score += 15;
            $details['location'] = ['match' => true, 'score' => 15];
        } else {
            $details['location'] = ['match' => false, 'score' => 0];
        }

        $finalScore = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;

        return [
            'score' => round($finalScore, 2),
            'details' => $details,
            'max_score' => $maxScore,
            'total_score' => $score
        ];
    }

    /**
     * Scopes
     */

    /**
     * Scope to get active applicants only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get available applicants
     */
    public function scopeAvailable($query)
    {
        return $query->where('work_status', 'available')->where('status', 'active');
    }

    /**
     * Scope to filter by education level
     */
    public function scopeEducationLevel($query, $level)
    {
        return $query->where('education_level', $level);
    }

    /**
     * Scope to filter by city
     */
    public function scopeCity($query, $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    /**
     * Scope to filter by age range
     */
    public function scopeAgeRange($query, $minAge = null, $maxAge = null)
    {
        if ($minAge) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= ?', [$minAge]);
        }
        if ($maxAge) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= ?', [$maxAge]);
        }
        return $query;
    }

    /**
     * Scope to filter by minimum experience
     */
    public function scopeMinExperience($query, $months)
    {
        return $query->where('total_work_experience_months', '>=', $months);
    }

    /**
     * Scope to filter by skills
     */
    public function scopeHasSkills($query, array $skills)
    {
        foreach ($skills as $skill) {
            $query->whereRaw('JSON_SEARCH(skills, "one", ?) IS NOT NULL', [$skill]);
        }
        return $query;
    }

    /**
     * Scope to filter by agent
     */
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope for full text search
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        })->orWhere('nik', 'like', "%{$search}%")
          ->orWhere('whatsapp_number', 'like', "%{$search}%")
          ->orWhere('school_name', 'like', "%{$search}%")
          ->orWhere('major', 'like', "%{$search}%");
    }
}