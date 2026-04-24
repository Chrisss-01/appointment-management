<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'certificate_type_id',
        'certificate_number',
        'purpose_type',
        'purpose_text',
        'medical_history',
        'additional_notes',
        'doctor_findings',
        'status',
        'rejection_reason',
        'verified_by',
        'approved_by',
        'verified_at',
        'approved_at',
        'file_path',
        'qr_code',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    // ── Relationships ───────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function certificateType(): BelongsTo
    {
        return $this->belongsTo(CertificateType::class);
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CertificateRequestDocument::class);
    }

    // ── Scopes ──────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDocumentsVerified($query)
    {
        return $query->where('status', 'documents_verified');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // ── Accessors ───────────────────────────────────────────────────

    /**
     * Virtual `purpose` attribute for backward-compatible display.
     * Returns the custom text when purpose_type is "other",
     * otherwise returns the preset label stored in purpose_type.
     */
    public function getPurposeAttribute(): string
    {
        if ($this->purpose_type === 'other') {
            return $this->purpose_text ?? 'Other (unspecified)';
        }

        return $this->purpose_type ?? '';
    }

    public function getRemarksRecommendationAttribute(): string
    {
        $purpose = trim($this->purpose);

        return 'Fit for ' . ($purpose !== '' ? $purpose : 'General Purpose');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // ── Status Helpers ──────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDocumentsVerified(): bool
    {
        return $this->status === 'documents_verified';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // ── Certificate Number Generator ────────────────────────────────

    public static function generateCertificateNumber(): string
    {
        $year = now()->year;
        $lastNumber = static::whereYear('created_at', $year)
            ->whereNotNull('certificate_number')
            ->count();

        return sprintf('UVTC-CLINIC-%d-%04d', $year, $lastNumber + 1);
    }
}
