<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'role',
        'status',
        'first_name',
        'last_name',
        'phone',
        'profile_picture',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Available user roles
     */
    const ROLES = [
        'super_admin' => 'Super Admin',
        'direktur' => 'Direktur',
        'hr_staff' => 'HR Staff',
        'agent' => 'Agent',
        'applicant' => 'Applicant'
    ];

    /**
     * Available user statuses
     */
    const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended'
    ];

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is admin (super_admin or direktur)
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super_admin', 'direktur']);
    }

    /**
     * Check if user is HR staff
     */
    public function isHRStaff(): bool
    {
        return $this->hasRole('hr_staff');
    }

    /**
     * Check if user is agent
     */
    public function isAgent(): bool
    {
        return $this->hasRole('agent');
    }

    /**
     * Check if user is applicant
     */
    public function isApplicant(): bool
    {
        return $this->hasRole('applicant');
    }

    /**
     * Relationships
     */

    /**
     * Get the agent profile if user is an agent
     */
    public function agent(): HasOne
    {
        return $this->hasOne(Agent::class);
    }

    /**
     * Get the applicant profile if user is an applicant
     */
    public function applicant(): HasOne
    {
        return $this->hasOne(Applicant::class);
    }

    /**
     * Get job postings created by this user (HR Staff)
     */
    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class, 'created_by');
    }

    /**
     * Get applications screened by this user
     */
    public function screenedApplications(): HasMany
    {
        return $this->hasMany(Application::class, 'screened_by');
    }

    /**
     * Get applications interviewed by this user
     */
    public function interviewedApplications(): HasMany
    {
        return $this->hasMany(Application::class, 'interviewed_by');
    }

    /**
     * Get placements processed by this user
     */
    public function processedPlacements(): HasMany
    {
        return $this->hasMany(Placement::class, 'placed_by');
    }

    /**
     * Get WhatsApp messages triggered by this user
     */
    public function triggeredWhatsAppLogs(): HasMany
    {
        return $this->hasMany(WhatsAppLog::class, 'triggered_by');
    }

    /**
     * Scopes
     */

    /**
     * Scope to get active users only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get users by role
     */
    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope to get admin users (super_admin and direktur)
     */
    public function scopeAdmins($query)
    {
        return $query->whereIn('role', ['super_admin', 'direktur']);
    }

    /**
     * Scope to get HR staff users
     */
    public function scopeHRStaff($query)
    {
        return $query->where('role', 'hr_staff');
    }

    /**
     * Scope to get agent users
     */
    public function scopeAgents($query)
    {
        return $query->where('role', 'agent');
    }

    /**
     * Scope to get applicant users
     */
    public function scopeApplicants($query)
    {
        return $query->where('role', 'applicant');
    }
}