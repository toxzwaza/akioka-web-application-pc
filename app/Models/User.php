<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'emp_no',
        'name',
        'password',
        'email',
        'gender_flg',
        'group_id',
        'position_id',
        'process_id',
        'is_admin',
        'dispatch_flg',
        'part_flg',
        'always_order_flg',
        'duty_flg',
        'fax_folder_name',
        'cybozu_flg',
        'del_flg',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'gender_flg' => 'integer',
        'group_id' => 'integer',
        'position_id' => 'integer',
        'process_id' => 'integer',
        'is_admin' => 'integer',
        'dispatch_flg' => 'integer',
        'part_flg' => 'integer',
        'always_order_flg' => 'integer',
        'duty_flg' => 'integer',
        'cybozu_flg' => 'integer',
        'del_flg' => 'integer',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
