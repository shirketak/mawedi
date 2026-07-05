<?php

namespace App\Models;

use App\Enums\DeactivationReason;
use App\Enums\LibyaGovernorate;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionType;
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
        'subscription_type',
        'subscription_status',
        'monthly_price',
        'usage_fee_per_booking',
        'subscription_starts_at',
        'subscription_ends_at',
        'free_trial_days',
        'trial_ends_at',
        'deactivation_reason',
        'deactivated_at',
    ];

    protected function casts(): array
    {
        return [
            'governorate' => LibyaGovernorate::class,
            'is_active' => 'boolean',
            'subscription_type' => SubscriptionType::class,
            'subscription_status' => SubscriptionStatus::class,
            'monthly_price' => 'decimal:2',
            'usage_fee_per_booking' => 'decimal:2',
            'subscription_starts_at' => 'date',
            'subscription_ends_at' => 'date',
            'free_trial_days' => 'integer',
            'trial_ends_at' => 'date',
            'deactivation_reason' => DeactivationReason::class,
            'deactivated_at' => 'datetime',
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

    public function wallet(): HasOne
    {
        return $this->hasOne(HospitalWallet::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function governorateLabel(): string
    {
        return $this->governorate?->label() ?? '';
    }

    public function walletBalance(): float
    {
        return (float) ($this->wallet?->balance ?? 0);
    }

    public function isOnTrial(): bool
    {
        return $this->subscription_status === SubscriptionStatus::Trial
            && $this->trial_ends_at
            && $this->trial_ends_at->isFuture();
    }

    public function subscriptionStatusLabel(): string
    {
        return $this->subscription_status?->label() ?? '—';
    }

    public function subscriptionTypeLabel(): string
    {
        return $this->subscription_type?->label() ?? '—';
    }
}
