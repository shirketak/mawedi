<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'hospital_id',
        'specialty_id',
        'name',
        'photo',
        'consultation_duration_minutes',
        'consultation_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'consultation_duration_minutes' => 'integer',
            'consultation_price' => 'decimal:2',
        ];
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function workingDays(): HasMany
    {
        return $this->hasMany(DoctorWorkingDay::class);
    }

    public function vacations(): HasMany
    {
        return $this->hasMany(DoctorVacation::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(DoctorSlot::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function priceLogs(): HasMany
    {
        return $this->hasMany(DoctorPriceLog::class);
    }
}
