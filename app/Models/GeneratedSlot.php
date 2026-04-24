<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GeneratedSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'availability_slot_id',
        'staff_id',
        'service_id',
        'date',
        'start_time',
        'end_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function availabilitySlot(): BelongsTo
    {
        return $this->belongsTo(AvailabilitySlot::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function appointment(): HasOne
    {
        return $this->hasOne(Appointment::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeBooked($query)
    {
        return $query->where('status', 'booked');
    }

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForService($query, int $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeBookableForStudents(Builder $query, ?CarbonInterface $referenceTime = null): Builder
    {
        $referenceTime = $referenceTime ? $referenceTime->copy() : now();
        $today = $referenceTime->toDateString();
        $leadTimeCutoff = $referenceTime->copy()->addMinutes(30);

        return $query
            ->available()
            ->where(function (Builder $query) use ($today, $leadTimeCutoff) {
                $query->whereDate('date', '>', $today);

                if ($leadTimeCutoff->toDateString() === $today) {
                    $query->orWhere(function (Builder $query) use ($today, $leadTimeCutoff) {
                        $query->whereDate('date', $today)
                            ->where('start_time', '>', $leadTimeCutoff->format('H:i:s'));
                    });
                }
            });
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function markAsBooked(): void
    {
        $this->update(['status' => 'booked']);
    }

    public function markAsAvailable(): void
    {
        $this->update(['status' => 'available']);
    }
}
