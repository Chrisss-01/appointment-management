<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'staff_id',
        'service_id',
        'generated_slot_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'reason',
        'additional_comments',
        'staff_notes',
        'rejection_reason',
        'cancellation_reason',
        'expiry_reason',
        'cancelled_at',
        'completed_at',
        'queue_number',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'queue_number' => 'integer',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // ── Relationships ───────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function generatedSlot(): BelongsTo
    {
        return $this->belongsTo(GeneratedSlot::class);
    }

    // ── Scopes ──────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('date', $date);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>', now()->toDateString())
                     ->whereIn('status', ['pending', 'approved']);
    }

    // ── Status Helpers ──────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved']);
    }

    public function reject(string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    public function complete(string $notes = null): void
    {
        $this->update([
            'status' => 'completed',
            'staff_notes' => $notes,
            'completed_at' => now(),
        ]);
    }

    public function cancel(?string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);
        $this->generatedSlot?->markAsAvailable();
    }

    public function cancelByStaff(string $reason): void
    {
        $this->update([
            'status' => 'cancelled_by_staff',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);
        $this->generatedSlot?->markAsAvailable();
    }

    public function expire(?string $reason = 'Not approved by clinic'): void
    {
        $this->update([
            'status' => 'expired',
            'expiry_reason' => $reason,
        ]);
    }
}
