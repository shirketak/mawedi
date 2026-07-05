<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DoctorPriceLog extends Model
{
    protected $fillable = [
        'doctor_id',
        'old_price',
        'new_price',
        'changed_by_type',
        'changed_by_id',
    ];

    protected function casts(): array
    {
        return [
            'old_price' => 'decimal:2',
            'new_price' => 'decimal:2',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function changedBy(): MorphTo
    {
        return $this->morphTo();
    }
}
