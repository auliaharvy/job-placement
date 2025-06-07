<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agent_code',
        'referral_code',
        'total_referrals',
        'successful_placements',
        'total_commission',
        'total_points',
        'level',
        'success_rate',
        'performance_metrics',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_commission' => 'decimal:2',
        'success_rate' => 'decimal:2',
        'performance_metrics' => 'array',
    ];

    /**
     * Available levels
     */
    const LEVELS = [
        'bronze' => 'Bronze',
        'silver' => 'Silver',
        'gold' => 'Gold',
        'platinum' => 'Platinum',
    ];

    /**
     * Available statuses
     */
    const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
    ];

    /**
     * Level thresholds
     */
    const LEVEL_THRESHOLDS = [
        'bronze' => 0,
        'silver' => 500,
        'gold' => 1500,
        'platinum' => 3000,
    ];

    /**
     * Commission rates per level
     */
    const COMMISSION_RATES = [
        'bronze' => 0.02, // 2%
        'silver' => 0.025, // 2.5%
        'gold' => 0.03, // 3%
        'platinum' => 0.035, // 3.5%
    ];

    /**
     * Relationships
     */

    /**
     * Get the user that owns the agent profile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all applicants referred by this agent
     */
    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }

    /**
     * Get all applications from this agent's referrals
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get all placements from this agent's referrals
     */
    public function placements(): HasMany
    {
        return $this->hasMany(Placement::class);
    }

    /**
     * Get all link clicks for this agent
     */
    public function linkClicks(): HasMany
    {
        return $this->hasMany(AgentLinkClick::class);
    }

    /**
     * Accessors
     */

    /**
     * Get the agent's full name from user relationship
     */
    public function getFullNameAttribute(): string
    {
        if ($this->user && $this->user->first_name && $this->user->last_name) {
            return $this->user->first_name. ' '. $this->user->last_name;
        }

        return $this->agent_code ?? 'Unknown Agent';
    }

    /**
     * Get the agent's email from user relationship
     */
    public function getEmailAttribute(): string
    {
        return $this->user && $this->user->email ? $this->user->email : '';
    }

    /**
     * Get commission rate based on level
     */
    public function getCommissionRateAttribute(): float
    {
        return self::COMMISSION_RATES[$this->level] ?? 0.02;
    }

    /**
     * Generate unique agent code
     */
    public static function generateAgentCode(): string
    {
        $lastAgent = self::orderBy('agent_code', 'desc')->first();

        if ($lastAgent && preg_match('/AGT(\d+)/', $lastAgent->agent_code, $matches)) {
            $lastNumber = (int) $matches[1];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'AGT' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique referral code
     */
    public static function generateReferralCode(string $firstName): string
    {
        $baseName = strtoupper(substr($firstName, 0, 4));
        $counter = 1;

        do {
            $code = $baseName . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $exists = self::where('referral_code', $code)->exists();
            $counter++;
        } while ($exists);

        return $code;
    }

    /**
     * Update agent statistics
     */
    public function updateStatistics(): void
    {
        $this->total_referrals = $this->applicants()->count();
        $this->successful_placements = $this->placements()->where('status', 'active')->count();

        // Calculate success rate
        if ($this->total_referrals > 0) {
            $this->success_rate = ($this->successful_placements / $this->total_referrals) * 100;
        } else {
            $this->success_rate = 0;
        }

        // Update level based on points
        $this->level = $this->calculateLevel();

        $this->save();
    }

    /**
     * Calculate agent level based on points
     */
    public function calculateLevel(): string
    {
        foreach (array_reverse(self::LEVEL_THRESHOLDS, true) as $level => $threshold) {
            if ($this->total_points >= $threshold) {
                return $level;
            }
        }
        return 'bronze';
    }

    /**
     * Add points to agent
     */
    public function addPoints(int $points, string $reason = null): void
    {
        $this->increment('total_points', $points);

        // Update level if threshold is reached
        $newLevel = $this->calculateLevel();
        if ($newLevel !== $this->level) {
            $this->update(['level' => $newLevel]);
        }

        // Log the points addition (optional - could be stored in a separate table)
        $metrics = $this->performance_metrics ?? [];
        $metrics['points_history'][] = [
            'points' => $points,
            'reason' => $reason,
            'date' => now()->toDateString(),
            'total_after' => $this->total_points + $points,
        ];
        $this->update(['performance_metrics' => $metrics]);
    }

    /**
     * Calculate commission for a placement
     */
    public function calculateCommission(float $salary): float
    {
        return $salary * $this->commission_rate;
    }

    /**
     * Add commission from successful placement
     */
    public function addCommission(float $amount, int $placementId): void
    {
        $this->increment('total_commission', $amount);

        // Add points for successful placement
        $this->addPoints(100, "Successful placement #{$placementId}");

        // Update performance metrics
        $metrics = $this->performance_metrics ?? [];
        $metrics['commission_history'][] = [
            'amount' => $amount,
            'placement_id' => $placementId,
            'date' => now()->toDateString(),
        ];
        $this->update(['performance_metrics' => $metrics]);
    }

    /**
     * Get QR code URL for registration
     */
    public function getQrCodeUrlAttribute(): string
    {
        return config('app.url') . "/register?ref=" . $this->referral_code;
    }

    /**
     * Get monthly performance stats
     */
    public function getMonthlyStats(int $year, int $month): array
    {
        $startDate = "{$year}-{$month}-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $referrals = $this->applicants()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $placements = $this->placements()
            ->whereBetween('start_date', [$startDate, $endDate])
            ->count();

        $commission = $this->placements()
            ->whereBetween('start_date', [$startDate, $endDate])
            ->sum('agent_commission');

        return [
            'referrals' => $referrals,
            'placements' => $placements,
            'commission' => $commission,
            'success_rate' => $referrals > 0 ? round(($placements / $referrals) * 100, 2) : 0,
        ];
    }

    /**
     * Scopes
     */

    /**
     * Scope to get active agents only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by level
     */
    public function scopeLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope to get top performers
     */
    public function scopeTopPerformers($query, $limit = 10)
    {
        return $query->active()
                    ->orderBy('total_points', 'desc')
                    ->orderBy('success_rate', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('user', function ($userQuery) use ($search) {
            $userQuery->where('first_name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
        })->orWhere('agent_code', 'like', "%{$search}%")
          ->orWhere('referral_code', 'like', "%{$search}%");
    }
}
