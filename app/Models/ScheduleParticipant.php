<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleParticipant extends Model
{
    protected $guarded = [];

    public function scheduleEvent(): BelongsTo
    {
        return $this->belongsTo(ScheduleEvent::class, 'schedule_event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
