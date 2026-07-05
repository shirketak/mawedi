<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HospitalWallet extends Model
{
    protected $fillable = [
        'hospital_id',
        'balance',
        'total_deposits',
        'total_deductions',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'total_deposits' => 'decimal:2',
            'total_deductions' => 'decimal:2',
        ];
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
