<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentLinkClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'referral_code',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'user_agent',
        'ip_address',
        'session_id',
        'browser_fingerprint',
        'clicked_at',
        'converted_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    /**
     * Get the agent that owns the click.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('clicked_at', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by agent
     */
    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope for filtering by UTM source
     */
    public function scopeBySource($query, $source)
    {
        return $query->where('utm_source', $source);
    }

    /**
     * Scope for filtering by UTM medium
     */
    public function scopeByMedium($query, $medium)
    {
        return $query->where('utm_medium', $medium);
    }

    /**
     * Scope for filtering by UTM campaign
     */
    public function scopeByCampaign($query, $campaign)
    {
        return $query->where('utm_campaign', $campaign);
    }

    /**
     * Scope for converted clicks only
     */
    public function scopeConverted($query)
    {
        return $query->whereNotNull('converted_at');
    }

    /**
     * Scope for unique visitors (by session and IP)
     */
    public function scopeUniqueVisitors($query)
    {
        return $query->distinct(['session_id', 'ip_address']);
    }

    /**
     * Check if this click has been converted
     */
    public function isConverted(): bool
    {
        return !is_null($this->converted_at);
    }

    /**
     * Mark this click as converted
     */
    public function markAsConverted(): void
    {
        $this->update(['converted_at' => now()]);
    }
}
