<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    protected $guarded = [];

    public function movieTag(): BelongsTo
    {
        return $this->belongsTo(MovieTag::class, 'movie_tag_id');
    }

    public function movieMemos(): HasMany
    {
        return $this->hasMany(MovieMemo::class, 'movie_id');
    }
}
