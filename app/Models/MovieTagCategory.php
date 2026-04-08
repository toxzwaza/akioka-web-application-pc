<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MovieTagCategory extends Model
{
    protected $guarded = [];

    public function movieTags(): HasMany
    {
        return $this->hasMany(MovieTag::class, 'movie_tag_category_id');
    }
}
