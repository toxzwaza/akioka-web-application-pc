<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotifyQueueUser extends Model
{
    protected $guarded = [];

    public function notifyQueue(): BelongsTo
    {
        return $this->belongsTo(NotifyQueue::class, 'notify_queue_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
