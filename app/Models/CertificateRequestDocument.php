<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateRequestDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_request_id',
        'certificate_type_document_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function certificateRequest(): BelongsTo
    {
        return $this->belongsTo(CertificateRequest::class);
    }

    public function typeDocument(): BelongsTo
    {
        return $this->belongsTo(CertificateTypeDocument::class, 'certificate_type_document_id');
    }
}
