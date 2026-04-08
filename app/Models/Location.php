<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $guarded = [];

    public function locationProcesses(): HasMany
    {
        return $this->hasMany(LocationProcess::class, 'location_id');
    }

    public function processes(): BelongsToMany
    {
        return $this->belongsToMany(Process::class, 'location_processes', 'location_id', 'process_id');
    }

    public function storageAddresses(): HasMany
    {
        return $this->hasMany(StorageAddress::class, 'location_id');
    }
}
