<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalFlow extends Model
{
    protected $guarded = [];

    public function conditions(): HasMany
    {
        return $this->hasMany(ApprovalFlowCondition::class, 'approval_flow_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalFlowStep::class, 'approval_flow_id');
    }
}
