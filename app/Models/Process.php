<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Process extends Model
{
    protected $guarded = [];

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function locationProcesses(): HasMany
    {
        return $this->hasMany(LocationProcess::class, 'process_id');
    }

    public function raspiData(): HasMany
    {
        return $this->hasMany(RaspiData::class, 'process_id');
    }

    public function stockProcesses(): HasMany
    {
        return $this->hasMany(StockProcess::class, 'process_id');
    }
}
