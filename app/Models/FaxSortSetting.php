<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaxSortSetting extends Model
{
    protected $guarded = [];

    public function faxGroup(): BelongsTo
    {
        return $this->belongsTo(FaxGroup::class, 'fax_group_id');
    }
}
