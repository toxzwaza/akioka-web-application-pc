<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleEvent extends Model
{
    protected $guarded = [];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ScheduleParticipant::class, 'schedule_event_id');
    }
}
