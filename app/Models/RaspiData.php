<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RaspiData extends Model
{
    protected $table = 'raspi_data';

    protected $guarded = [];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class, 'process_id');
    }
}
