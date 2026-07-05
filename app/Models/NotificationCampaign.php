<?php

namespace App\Models;

use App\Enums\NotificationTargetType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationCampaign extends Model
{
    protected $fillable = [
        'title',
        'body',
        'target_type',
        'target_id',
        'status',
        'created_by_type',
        'created_by_id',
        'scheduled_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'target_type' => NotificationTargetType::class,
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function createdBy(): MorphTo
    {
        return $this->morphTo();
    }
}
