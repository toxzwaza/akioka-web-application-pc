<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaxGroup extends Model
{
    protected $guarded = [];

    public function faxSortSettings(): HasMany
    {
        return $this->hasMany(FaxSortSetting::class, 'fax_group_id');
    }

    public function faxUserGroups(): HasMany
    {
        return $this->hasMany(FaxUserGroup::class, 'fax_group_id');
    }
}
