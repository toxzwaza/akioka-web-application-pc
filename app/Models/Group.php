<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $table = 'groups';

    protected $guarded = [];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'group_id');
    }
}
