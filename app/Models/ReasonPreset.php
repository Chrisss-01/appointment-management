<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReasonPreset extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'label',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
