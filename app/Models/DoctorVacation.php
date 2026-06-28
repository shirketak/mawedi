<?php

namespace App\Models;

use App\Enums\VacationType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorVacation extends Model
{
    use HasUuid;

    protected $fillable = [
        'doctor_id',
        'date',
        'type',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'type' => VacationType::class,
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
