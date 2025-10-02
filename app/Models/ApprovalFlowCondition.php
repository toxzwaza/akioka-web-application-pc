<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalFlowCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_flow_id',
        'condition_type',
        'operator',
        'condition_value'
    ];

    public function approvalFlow()
    {
        return $this->belongsTo(ApprovalFlow::class);
    }
}
