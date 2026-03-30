<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'staff_id',
        'appointment_id',
        'record_type',
        'service_name',
        'chief_complaint',
        'diagnosis',
        'treatment',
        'prescription',
        'vital_signs',
        'visual_acuity',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'vital_signs' => 'array',
            'visual_acuity' => 'array',
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

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    // ── Scopes ──────────────────────────────────────────────────────

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('record_type', $type);
    }
}
