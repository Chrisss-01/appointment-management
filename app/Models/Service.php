<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'duration_minutes',
        'color',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'duration_minutes' => 'integer',
        ];
    }

    // ── Relationships ───────────────────────────────────────────────

    public function availabilitySlots(): HasMany
    {
        return $this->hasMany(AvailabilitySlot::class);
    }

    public function generatedSlots(): HasMany
    {
        return $this->hasMany(GeneratedSlot::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function reasonPresets(): HasMany
    {
        return $this->hasMany(ReasonPreset::class);
    }

    // ── Scopes ──────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the service has any available slots today or in the future.
     */
    public function isAvailable(): bool
    {
        return $this->generatedSlots()
            ->where('status', 'available')
            ->where('date', '>=', now()->toDateString())
            ->exists();
    }
}
