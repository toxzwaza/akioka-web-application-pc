<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotifyQueue extends Model
{
    protected $guarded = [];

    public function notifyQueueUsers(): HasMany
    {
        return $this->hasMany(NotifyQueueUser::class, 'notify_queue_id');
    }
}
