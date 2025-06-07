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
     * Check if applicant matches job requirements
     */
    public function matchesJobRequirements(JobPosting $job): array
    {
        $score = 0;
        $maxScore = 0;
        $details = [];
        $matches = true;

        // Check education level requirement (20 points)
        $maxScore += 20;
        if (!empty($job->required_education_levels)) {
            if (in_array($this->education_level, $job->required_education_levels)) {
                $score += 20;
                $details['education'] = ['match' => true, 'message' => 'Education level matches'];
            } else {
                $matches = false;
                $details['education'] = ['match' => false, 'message' => 'Education level does not match requirements'];
            }
        } else {
            $score += 20;
            $details['education'] = ['match' => true, 'message' => 'No specific education requirement'];
        }

        // Check minimum experience requirement (25 points)
        $maxScore += 25;
        if ($job->min_experience_months > 0) {
            $applicantExp = $this->total_work_experience_months ?? 0;
            if ($applicantExp >= $job->min_experience_months) {
                $score += 25;
                $details['experience'] = ['match' => true, 'message' => "Has {$applicantExp} months experience (required: {$job->min_experience_months})"];
            } else {
                $matches = false;
                $score += max(0, ($applicantExp / $job->min_experience_months) * 25);
                $details['experience'] = ['match' => false, 'message' => "Has {$applicantExp} months experience, but {$job->min_experience_months} months required"];
            }
        } else {
            $score += 25;
            $details['experience'] = ['match' => true, 'message' => 'No experience requirement'];
        }

        // Check work location preference (15 points)
        $maxScore += 15;
        if (!empty($this->preferred_locations) && !empty($job->work_city)) {
            $hasLocationMatch = false;
            foreach ($this->preferred_locations as $location) {
                if (stripos($location, $job->work_city) !== false ||
                    stripos($job->work_city, $location) !== false) {
                    $hasLocationMatch = true;
                    break;
                }
            }
            if ($hasLocationMatch) {
                $score += 15;
                $details['location'] = ['match' => true, 'message' => 'Location preference matches'];
            } else {
                $matches = false;
                $details['location'] = ['match' => false, 'message' => 'Location preference does not match'];
            }
        } else {
            $score += 15;
            $details['location'] = ['match' => true, 'message' => 'No location preference specified'];
        }

        // Check skills match (30 points)
        $maxScore += 30;
        if (!empty($job->required_skills) && !empty($this->skills)) {
            $matchedSkills = [];
            foreach ($job->required_skills as $requiredSkill) {
                foreach ($this->skills as $applicantSkill) {
                    if (stripos($applicantSkill, $requiredSkill) !== false ||
                        stripos($requiredSkill, $applicantSkill) !== false) {
                        $matchedSkills[] = $requiredSkill;
                        break;
                    }
                }
            }

            if (!empty($matchedSkills)) {
                $skillScore = (count($matchedSkills) / count($job->required_skills)) * 30;
                $score += $skillScore;
                $details['skills'] = [
                    'match' => count($matchedSkills) === count($job->required_skills),
                    'message' => 'Matched skills: ' . implode(', ', $matchedSkills),
                    'matched_count' => count($matchedSkills),
                    'required_count' => count($job->required_skills)
                ];

                if (count($matchedSkills) < count($job->required_skills)) {
                    $matches = false;
                }
            } else {
                $matches = false;
                $details['skills'] = ['match' => false, 'message' => 'No matching skills found'];
            }
        } else {
            $score += 30;
            $details['skills'] = ['match' => true, 'message' => 'No specific skills required'];
        }

        // Check salary expectation (10 points)
        $maxScore += 10;
        if ($this->expected_salary_min && $job->salary_max) {
            if ($this->expected_salary_min <= $job->salary_max) {
                $score += 10;
                $details['salary'] = ['match' => true, 'message' => 'Salary expectation is within range'];
            } else {
                $matches = false;
                $details['salary'] = ['match' => false, 'message' => 'Salary expectation exceeds maximum offer'];
            }
        } else {
            $score += 10;
            $details['salary'] = ['match' => true, 'message' => 'No salary expectation specified'];
        }

        // Check availability status
        if (!$this->isAvailable()) {
            $matches = false;
            $details['availability'] = ['match' => false, 'message' => 'Applicant is not currently available'];
        } else {
            $details['availability'] = ['match' => true, 'message' => 'Applicant is available'];
        }

        $finalScore = min(100, round(($score / $maxScore) * 100));

        return [
            'matches' => $matches,
            'score' => $finalScore,
            'details' => $details
        ];
    }

    /**
     * Get matching score for a job (0-100)
     */
    public function getJobMatchingScore(JobPosting $job): int
    {
        $score = 0;
        $maxScore = 0;

        // Education match (20 points)
        $maxScore += 20;
        if (!empty($job->required_education_levels) && in_array($this->education_level, $job->required_education_levels)) {
            $score += 20;
        }

        // Experience match (25 points)
        $maxScore += 25;
        if ($job->min_experience_months > 0) {
            $applicantExp = $this->total_work_experience_months ?? 0;
            if ($applicantExp >= $job->min_experience_months) {
                $score += 25;
            } else {
                // Partial score for partial experience
                $score += max(0, ($applicantExp / $job->min_experience_months) * 25);
            }
        } else {
            $score += 25; // No experience required
        }

        // Skills match (30 points)
        $maxScore += 30;
        if (!empty($job->required_skills) && !empty($this->skills)) {
            $matchedSkills = 0;
            foreach ($job->required_skills as $requiredSkill) {
                foreach ($this->skills as $applicantSkill) {
                    if (stripos($applicantSkill, $requiredSkill) !== false ||
                        stripos($requiredSkill, $applicantSkill) !== false) {
                        $matchedSkills++;
                        break;
                    }
                }
            }
            $score += ($matchedSkills / count($job->required_skills)) * 30;
        } else {
            $score += 30; // No specific skills required
        }

        // Location match (15 points)
        $maxScore += 15;
        if (!empty($this->preferred_locations) && !empty($job->work_city)) {
            foreach ($this->preferred_locations as $location) {
                if (stripos($location, $job->work_city) !== false ||
                    stripos($job->work_city, $location) !== false) {
                    $score += 15;
                    break;
                }
            }
        } else {
            $score += 15; // No location preference
        }

        // Salary match (10 points)
        $maxScore += 10;
        if ($this->expected_salary_min && $job->salary_max) {
            if ($this->expected_salary_min <= $job->salary_max) {
                $score += 10;
            }
        } else {
            $score += 10; // No salary expectation or job salary
        }

        return min(100, round(($score / $maxScore) * 100));
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
