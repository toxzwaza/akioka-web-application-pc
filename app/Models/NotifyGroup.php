<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotifyGroup extends Model
{
    protected $guarded = [];

    public function notifyGroupUsers(): HasMany
    {
        return $this->hasMany(NotifyGroupUser::class, 'notify_group_id');
    }
}
