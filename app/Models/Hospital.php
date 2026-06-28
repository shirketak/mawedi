<?php

namespace App\Models;

use App\Enums\LibyaGovernorate;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hospital extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'name',
        'logo',
        'map_url',
        'governorate',
        'phone',
        'phone_secondary',
        'email',
        'website',
        'address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'governorate' => LibyaGovernorate::class,
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(HospitalUser::class);
    }

    public function primaryUser(): HasOne
    {
        return $this->hasOne(HospitalUser::class)->oldestOfMany();
    }

    public function specialties(): BelongsToMany
    {
        return $this->belongsToMany(Specialty::class, 'hospital_specialty')
            ->withTimestamps();
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function rescheduleLogs(): HasMany
    {
        return $this->hasMany(BookingRescheduleLog::class);
    }

    public function governorateLabel(): string
    {
        return $this->governorate?->label() ?? '';
    }
}
