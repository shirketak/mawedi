<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorWorkingPeriod extends Model
{
    protected $fillable = [
        'doctor_working_day_id',
        'start_time',
        'end_time',
    ];

    public function workingDay(): BelongsTo
    {
        return $this->belongsTo(DoctorWorkingDay::class, 'doctor_working_day_id');
    }
}
