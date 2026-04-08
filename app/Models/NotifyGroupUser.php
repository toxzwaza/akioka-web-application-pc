<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotifyGroupUser extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notifyGroup(): BelongsTo
    {
        return $this->belongsTo(NotifyGroup::class, 'notify_group_id');
    }
}
