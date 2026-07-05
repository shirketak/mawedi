<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'hospital_id',
        'doctor_id',
        'specialty_id',
        'doctor_slot_id',
        'patient_id',
        'patient_name',
        'patient_phone',
        'booking_date',
        'booking_time',
        'status',
        'payment_status',
        'consultation_price',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'status' => BookingStatus::class,
            'payment_status' => PaymentStatus::class,
            'consultation_price' => 'decimal:2',
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

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(DoctorSlot::class, 'doctor_slot_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
