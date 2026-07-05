<?php

namespace App\Models;

use App\Enums\WalletTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'hospital_id',
        'hospital_wallet_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'reason',
        'performed_by_type',
        'performed_by_id',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'type' => WalletTransactionType::class,
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(HospitalWallet::class, 'hospital_wallet_id');
    }

    public function performedBy(): MorphTo
    {
        return $this->morphTo();
    }
}
