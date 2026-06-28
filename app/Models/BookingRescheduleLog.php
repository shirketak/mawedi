<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BookingRescheduleLog extends Model
{
    use HasUuid;

    protected $fillable = [
        'hospital_id',
        'doctor_id',
        'original_date',
        'reason',
        'rescheduled_by_type',
        'rescheduled_by_id',
        'details',
    ];

    protected function casts(): array
    {
        return [
            'original_date' => 'date',
            'details' => 'array',
        ];
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function rescheduledBy(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'rescheduled_by_type', 'rescheduled_by_id');
    }
}
