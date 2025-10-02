<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalFlowStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_flow_id',
        'step_order',
        'approver_user_id',
        'is_required'
    ];

    protected $casts = [
        'is_required' => 'boolean'
    ];

    public function approvalFlow()
    {
        return $this->belongsTo(ApprovalFlow::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
}
