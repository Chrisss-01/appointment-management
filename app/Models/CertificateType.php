<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function requiredDocuments(): HasMany
    {
        return $this->hasMany(CertificateTypeDocument::class);
    }

    public function purposePresets(): HasMany
    {
        return $this->hasMany(CertificatePurposePreset::class);
    }

    public function certificateRequests(): HasMany
    {
        return $this->hasMany(CertificateRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
