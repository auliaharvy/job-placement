<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppLog extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_logs';

    protected $fillable = [
        'message_id',
        'session_id',
        'phone_number',
        'message_type',
        'message_content',
        'template_name',
        'template_variables',
        'media_file_path',
        'media_caption',
        'status',
        'sent_at',
        'delivered_at',
        'read_at',
        'failed_at',
        'error_message',
        'retry_count',
        'next_retry_at',
        'context_type',
        'context_id',
        'triggered_by',
        'applicant_id',
        'job_posting_id',
        'application_id',
        'broadcast_id',
        'is_broadcast',
        'broadcast_sequence',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'template_variables' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'failed_at' => 'datetime',
        'next_retry_at' => 'datetime',
        'is_broadcast' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Available message types
     */
    const MESSAGE_TYPES = [
        'text' => 'Text Message',
        'image' => 'Image',
        'document' => 'Document',
        'template' => 'Template Message',
        'location' => 'Location',
    ];

    /**
     * Available statuses
     */
    const STATUSES = [
        'pending' => 'Pending',
        'sent' => 'Sent',
        'delivered' => 'Delivered',
        'read' => 'Read',
        'failed' => 'Failed',
        'expired' => 'Expired',
    ];

    /**
     * Available context types
     */
    const CONTEXT_TYPES = [
        'job_broadcast' => 'Job Broadcast',
        'selection_update' => 'Selection Update',
        'application_rejected' => 'Application Rejected',
        'application_accepted' => 'Application Accepted',
        'placement_reminder' => 'Placement Reminder',
        'contract_expiry' => 'Contract Expiry',
        'welcome_message' => 'Welcome Message',
        'manual_message' => 'Manual Message',
    ];

    /**
     * Relationships
     */

    /**
     * Get the user who triggered this message
     */
    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    /**
     * Get the applicant associated with this message
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Get the job posting associated with this message
     */
    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * Get the application associated with this message
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Accessors
     */

    /**
     * Get delivery status display
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Get message type display
     */
    public function getMessageTypeDisplayAttribute(): string
    {
        return self::MESSAGE_TYPES[$this->message_type] ?? $this->message_type;
    }

    /**
     * Get context type display
     */
    public function getContextTypeDisplayAttribute(): string
    {
        return self::CONTEXT_TYPES[$this->context_type] ?? $this->context_type;
    }

    /**
     * Check if message is delivered
     */
    public function isDelivered(): bool
    {
        return in_array($this->status, ['delivered', 'read']);
    }

    /**
     * Check if message failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if message can be retried
     */
    public function canBeRetried(): bool
    {
        return $this->status === 'failed' && $this->retry_count < 3;
    }

    /**
     * Mark message as sent
     */
    public function markAsSent(string $messageId = null): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'message_id' => $messageId,
        ]);
    }

    /**
     * Mark message as delivered
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'status' => 'read',
            'read_at' => now(),
        ]);
    }

    /**
     * Mark message as failed
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
            'next_retry_at' => $this->canBeRetried() ? now()->addMinutes(5 * $this->retry_count) : null,
        ]);
    }

    /**
     * Get broadcast summary statistics
     */
    public static function getBroadcastStats(string $broadcastId): array
    {
        $logs = self::where('broadcast_id', $broadcastId)->get();
        
        return [
            'total' => $logs->count(),
            'pending' => $logs->where('status', 'pending')->count(),
            'sent' => $logs->where('status', 'sent')->count(),
            'delivered' => $logs->where('status', 'delivered')->count(),
            'read' => $logs->where('status', 'read')->count(),
            'failed' => $logs->where('status', 'failed')->count(),
            'delivery_rate' => $logs->count() > 0 ? round(($logs->whereIn('status', ['delivered', 'read'])->count() / $logs->count()) * 100, 2) : 0,
        ];
    }

    /**
     * Scopes
     */

    /**
     * Scope to get pending messages
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get failed messages that can be retried
     */
    public function scopeCanRetry($query)
    {
        return $query->where('status', 'failed')
                    ->where('retry_count', '<', 3)
                    ->where(function ($q) {
                        $q->whereNull('next_retry_at')
                          ->orWhere('next_retry_at', '<=', now());
                    });
    }

    /**
     * Scope to filter by broadcast
     */
    public function scopeBroadcast($query, $broadcastId)
    {
        return $query->where('broadcast_id', $broadcastId);
    }

    /**
     * Scope to filter by context type
     */
    public function scopeContextType($query, $contextType)
    {
        return $query->where('context_type', $contextType);
    }

    /**
     * Scope to filter by phone number
     */
    public function scopePhoneNumber($query, $phoneNumber)
    {
        return $query->where('phone_number', $phoneNumber);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('phone_number', 'like', "%{$search}%")
              ->orWhere('message_content', 'like', "%{$search}%")
              ->orWhere('broadcast_id', 'like', "%{$search}%")
              ->orWhereHas('applicant.user', function ($userQuery) use ($search) {
                  $userQuery->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%");
              });
        });
    }
}