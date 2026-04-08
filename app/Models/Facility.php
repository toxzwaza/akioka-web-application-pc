<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    protected $guarded = [];

    public function scheduleEvents(): HasMany
    {
        return $this->hasMany(ScheduleEvent::class, 'facility_id');
    }
}
