<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockProcess extends Model
{
    protected $guarded = [];

    public function accountItem(): BelongsTo
    {
        return $this->belongsTo(AccountItem::class, 'account_item_id');
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class, 'process_id');
    }
}
