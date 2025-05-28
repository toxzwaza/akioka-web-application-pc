<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifyQueue extends Model
{
    use HasFactory;

    public function notifyQueueUsers()
    {
        return $this->hasMany(NotifyQueueUser::class);
    }
}
