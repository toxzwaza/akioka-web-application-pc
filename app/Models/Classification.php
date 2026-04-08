<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classification extends Model
{
    protected $guarded = [];

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'classification_id');
    }
}
