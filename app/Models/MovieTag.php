<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovieTag extends Model
{
    protected $guarded = [];

    public function movieTagCategory(): BelongsTo
    {
        return $this->belongsTo(MovieTagCategory::class, 'movie_tag_category_id');
    }
}
