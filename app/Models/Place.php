<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    protected $guarded = [];

    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class, 'place_id');
    }

    public function processes(): HasMany
    {
        return $this->hasMany(Process::class, 'place_id');
    }
}
