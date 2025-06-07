<?php
/**
 * File Path: /backend/app/Services/JobMatchingService.php
 * Service untuk mencocokan pelamar dengan lowongan kerja
 */

namespace App\Services;

use App\Models\JobPosting;
use App\Models\Applicant;
use Illuminate\Support\Collection;

class JobMatchingService
{
    /**
     * Mencari pelamar yang cocok dengan lowongan kerja
     */
    public function findMatchingApplicants(JobPosting $job): Collection
    {
        $query = Applicant::where('status', Applicant::STATUS_ACTIVE)
                         ->where('work_status', Applicant::AVAILABILITY_AVAILABLE);

        // Age filter
        if ($job->min_age || $job->max_age) {
            if ($job->min_age) {
                $maxBirthDate = now()->subYears($job->min_age)->format('Y-m-d');
                $query->where('birth_date', '<=', $maxBirthDate);
            }
            if ($job->max_age) {
                $minBirthDate = now()->subYears($job->max_age + 1)->addDay()->format('Y-m-d');
                $query->where('birth_date', '>=', $minBirthDate);
            }
        }

        // Education filter
        if ($job->required_education_levels && count($job->required_education_levels) > 0) {
            $query->whereIn('education_level', $job->required_education_levels);
        }

        // Gender filter
        if ($job->preferred_genders && count($job->preferred_genders) > 0) {
            $query->whereIn('gender', $job->preferred_genders);
        }

        // Experience filter
        if ($job->min_experience_months) {
            $query->where('total_work_experience_months', '>=', $job->min_experience_months);
        }

        // Skills filter
        if ($job->required_skills && count($job->required_skills) > 0) {
            foreach ($job->required_skills as $skill) {
                $query->whereJsonContains('skills', $skill);
            }
        }

        // Location preference (optional - could be enhanced)
        // For now, we don't filter by location unless specified

        return $query->with(['user', 'agent'])->get();
    }

    /**
     * Menghitung skor kesesuaian antara lowongan dan pelamar
     */
    public function calculateMatchingScore(JobPosting $job, Applicant $applicant): float
    {
        $score = 0;
        $totalWeight = 0;

        // Age compatibility (weight: 15%)
        $ageScore = $this->calculateAgeScore($job, $applicant);
        $score += $ageScore * 0.15;
        $totalWeight += 0.15;

        // Education compatibility (weight: 25%)
        $educationScore = $this->calculateEducationScore($job, $applicant);
        $score += $educationScore * 0.25;
        $totalWeight += 0.25;

        // Experience compatibility (weight: 30%)
        $experienceScore = $this->calculateExperienceScore($job, $applicant);
        $score += $experienceScore * 0.30;
        $totalWeight += 0.30;

        // Skills compatibility (weight: 25%)
        $skillsScore = $this->calculateSkillsScore($job, $applicant);
        $score += $skillsScore * 0.25;
        $totalWeight += 0.25;

        // Gender compatibility (weight: 5%)
        $genderScore = $this->calculateGenderScore($job, $applicant);
        $score += $genderScore * 0.05;
        $totalWeight += 0.05;

        return $totalWeight > 0 ? ($score / $totalWeight) * 100 : 0;
    }

    /**
     * Menghitung skor kesesuaian usia
     */
    private function calculateAgeScore(JobPosting $job, Applicant $applicant): float
    {
        if (!$applicant->birth_date) {
            return 0.5; // Default score if birth date not available
        }

        $applicantAge = $applicant->birth_date->age;

        // Perfect match if within range
        if ((!$job->min_age || $applicantAge >= $job->min_age) &&
            (!$job->max_age || $applicantAge <= $job->max_age)) {
            return 1.0;
        }

        // Partial match if close to range
        $tolerance = 2; // 2 years tolerance
        if ($job->min_age && $applicantAge < $job->min_age) {
            $diff = $job->min_age - $applicantAge;
            return $diff <= $tolerance ? (1 - ($diff / $tolerance) * 0.5) : 0;
        }

        if ($job->max_age && $applicantAge > $job->max_age) {
            $diff = $applicantAge - $job->max_age;
            return $diff <= $tolerance ? (1 - ($diff / $tolerance) * 0.5) : 0;
        }

        return 0;
    }

    /**
     * Menghitung skor kesesuaian pendidikan
     */
    private function calculateEducationScore(JobPosting $job, Applicant $applicant): float
    {
        if (!$job->required_education_levels || count($job->required_education_levels) === 0) {
            return 1.0;
        }

        // Perfect match if applicant's education is in required list
        if (in_array($applicant->education_level, $job->required_education_levels)) {
            return 1.0;
        }

        // Partial score based on education hierarchy
        $educationHierarchy = $this->getEducationHierarchy();
        $applicantLevel = array_search($applicant->education_level, $educationHierarchy);

        if ($applicantLevel === false) return 0;

        // Check if applicant has higher education than any required
        $maxRequiredLevel = -1;
        foreach ($job->required_education_levels as $reqEdu) {
            $reqLevel = array_search($reqEdu, $educationHierarchy);
            if ($reqLevel !== false && $reqLevel > $maxRequiredLevel) {
                $maxRequiredLevel = $reqLevel;
            }
        }

        if ($maxRequiredLevel === -1) return 0;

        // Higher education gets full score
        if ($applicantLevel >= $maxRequiredLevel) {
            return 1.0;
        }

        // Lower education gets partial score
        $levelDiff = $maxRequiredLevel - $applicantLevel;
        return max(0, 1 - ($levelDiff * 0.3));
    }

    /**
     * Menghitung skor kesesuaian pengalaman
     */
    private function calculateExperienceScore(JobPosting $job, Applicant $applicant): float
    {
        if (!$job->min_experience_months) return 1.0;

        $applicantExperienceMonths = $applicant->total_work_experience_months ?? 0;
        $requiredExperienceMonths = $job->min_experience_months;

        // Perfect match if meets or exceeds requirement
        if ($applicantExperienceMonths >= $requiredExperienceMonths) {
            return 1.0;
        }

        // Partial score for less experience
        $experienceRatio = $applicantExperienceMonths / $requiredExperienceMonths;
        return max(0, $experienceRatio);
    }

    /**
     * Menghitung skor kesesuaian skill
     */
    private function calculateSkillsScore(JobPosting $job, Applicant $applicant): float
    {
        if (!$job->required_skills || count($job->required_skills) === 0) {
            return 1.0;
        }

        $applicantSkills = $applicant->skills ?? [];
        if (empty($applicantSkills)) return 0;

        $matchedSkills = array_intersect($job->required_skills, $applicantSkills);
        $matchRatio = count($matchedSkills) / count($job->required_skills);

        return $matchRatio;
    }

    /**
     * Menghitung skor kesesuaian gender
     */
    private function calculateGenderScore(JobPosting $job, Applicant $applicant): float
    {
        if (!$job->preferred_genders || count($job->preferred_genders) === 0) {
            return 1.0; // No gender preference
        }

        return in_array($applicant->gender, $job->preferred_genders) ? 1.0 : 0.5;
    }

    /**
     * Hirarki tingkat pendidikan
     */
    private function getEducationHierarchy(): array
    {
        return [
            JobPosting::EDUCATION_SD,
            JobPosting::EDUCATION_SMP,
            JobPosting::EDUCATION_SMA,
            JobPosting::EDUCATION_D3,
            JobPosting::EDUCATION_S1,
            JobPosting::EDUCATION_S2,
        ];
    }

    /**
     * Mendapatkan kriteria matching untuk lowongan
     */
    public function getMatchCriteria(JobPosting $job): array
    {
        $criteria = [];

        if ($job->min_age || $job->max_age) {
            $ageRange = '';
            if ($job->min_age && $job->max_age) {
                $ageRange = "{$job->min_age}-{$job->max_age} tahun";
            } elseif ($job->min_age) {
                $ageRange = "minimal {$job->min_age} tahun";
            } else {
                $ageRange = "maksimal {$job->max_age} tahun";
            }
            $criteria['age'] = $ageRange;
        }

        if ($job->required_education_levels && count($job->required_education_levels) > 0) {
            $criteria['education'] = 'Minimal ' . implode(', ', array_map('strtoupper', $job->required_education_levels));
        }

        if ($job->preferred_genders && count($job->preferred_genders) > 0) {
            $criteria['gender'] = 'Prefer: ' . implode(', ', array_map('ucfirst', $job->preferred_genders));
        }

        if ($job->min_experience_months) {
            $criteria['experience'] = "Minimal " . round($job->min_experience_months / 12, 1) . " tahun pengalaman";
        }

        if ($job->required_skills && count($job->required_skills) > 0) {
            $criteria['skills'] = implode(', ', $job->required_skills);
        }

        return $criteria;
    }

    /**
     * Mencari lowongan yang cocok untuk pelamar
     */
    public function findMatchingJobsForApplicant(Applicant $applicant): Collection
    {
        $query = JobPosting::where('status', JobPosting::STATUS_ACTIVE)
                          ->where('application_deadline', '>', now());

        // Age filter
        if ($applicant->birth_date) {
            $age = $applicant->birth_date->age;
            $query->where(function($q) use ($age) {
                $q->where(function($subQ) use ($age) {
                    $subQ->whereNull('min_age')->orWhere('min_age', '<=', $age);
                })->where(function($subQ) use ($age) {
                    $subQ->whereNull('max_age')->orWhere('max_age', '>=', $age);
                });
            });
        }

        // Education filter
        if ($applicant->education_level) {
            $query->where(function($q) use ($applicant) {
                $q->whereJsonContains('required_education_levels', $applicant->education_level)
                  ->orWhereNull('required_education_levels');
            });
        }

        // Experience filter
        if ($applicant->total_work_experience_months !== null) {
            $query->where(function($q) use ($applicant) {
                $q->whereNull('min_experience_months')
                  ->orWhere('min_experience_months', '<=', $applicant->total_work_experience_months);
            });
        }

        // Gender filter
        $query->where(function($q) use ($applicant) {
            $q->whereNull('preferred_genders')
              ->orWhereJsonContains('preferred_genders', $applicant->gender);
        });

        return $query->with(['company'])->get();
    }

    /**
     * Mencari lowongan berdasarkan lokasi
     */
    public function findNearbyJobs(Applicant $applicant, int $radiusKm = 50): Collection
    {
        // Simple implementation using city/province matching
        // For more advanced implementation, use coordinates and distance calculation

        $query = JobPosting::where('status', JobPosting::STATUS_ACTIVE)
                          ->where('application_deadline', '>', now());

        // Match by city first
        if ($applicant->city) {
            $query->where(function($q) use ($applicant) {
                $q->where('location', 'LIKE', "%{$applicant->city}%");

                // Also match by province if city doesn't match
                if ($applicant->province) {
                    $q->orWhere('location', 'LIKE', "%{$applicant->province}%");
                }
            });
        }

        return $query->with(['company'])->get();
    }

    /**
     * Analisis trend matching
     */
    public function getMatchingTrends(): array
    {
        $trends = [];

        // Most common skill requirements
        $skillTrends = JobPosting::where('status', JobPosting::STATUS_ACTIVE)
                                ->whereNotNull('required_skills')
                                ->get()
                                ->pluck('required_skills')
                                ->flatten()
                                ->countBy()
                                ->sortDesc()
                                ->take(10);

        $trends['popular_skills'] = $skillTrends->toArray();

        // Education level distributions
        $educationTrends = JobPosting::where('status', JobPosting::STATUS_ACTIVE)
                                   ->whereNotNull('required_education_levels')
                                   ->get()
                                   ->flatMap(function($job) {
                                       return $job->required_education_levels ?? [];
                                   })
                                   ->countBy();

        $trends['education_requirements'] = $educationTrends->toArray();

        // Age group preferences
        $ageGroups = JobPosting::where('status', JobPosting::STATUS_ACTIVE)
                              ->where(function($q) {
                                  $q->whereNotNull('min_age')->orWhereNotNull('max_age');
                              })
                              ->get()
                              ->map(function($job) {
                                  $min = $job->min_age ?? 18;
                                  $max = $job->max_age ?? 60;

                                  if ($max <= 25) return '18-25';
                                  if ($max <= 35) return '26-35';
                                  if ($max <= 45) return '36-45';
                                  return '46+';
                              })
                              ->countBy();

        $trends['age_group_preferences'] = $ageGroups->toArray();

        // Geographic distributions
        $locationTrends = JobPosting::where('status', JobPosting::STATUS_ACTIVE)
                                  ->get()
                                  ->groupBy('work_city')
                                  ->map(function($jobs) {
                                      return $jobs->count();
                                  })
                                  ->sortDesc()
                                  ->take(10);

        $trends['location_distribution'] = $locationTrends->toArray();

        return $trends;
    }

    /**
     * Mendapatkan rekomendasi pelamar dengan skor
     */
    public function getRecommendedApplicants(JobPosting $job, int $limit = 10): Collection
    {
        $applicants = $this->findMatchingApplicants($job);

        return $applicants->map(function($applicant) use ($job) {
            $applicant->matching_score = $this->calculateMatchingScore($job, $applicant);
            return $applicant;
        })->sortByDesc('matching_score')->take($limit);
    }

    /**
     * Mendapatkan rekomendasi lowongan dengan skor
     */
    public function getRecommendedJobs(Applicant $applicant, int $limit = 10): Collection
    {
        $jobs = $this->findMatchingJobsForApplicant($applicant);

        return $jobs->map(function($job) use ($applicant) {
            $job->matching_score = $this->calculateMatchingScore($job, $applicant);
            return $job;
        })->sortByDesc('matching_score')->take($limit);
    }
}
