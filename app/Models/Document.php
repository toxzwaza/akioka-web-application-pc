<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documentImages(): HasMany
    {
        return $this->hasMany(DocumentImage::class, 'document_id');
    }

    public function documentStocks(): HasMany
    {
        return $this->hasMany(DocumentStock::class, 'document_id');
    }
}
