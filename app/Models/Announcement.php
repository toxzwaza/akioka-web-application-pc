<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * 指定日（省略時は本日）に掲載期間内かつ有効なお知らせのみ。
     */
    public function scopeActiveForDate(Builder $query, ?string $date = null): Builder
    {
        $date = $date ?? now()->toDateString();

        return $query
            ->where('is_active', 1)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date);
    }
}
