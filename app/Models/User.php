<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'emp_no',
        'gender_flg',
        'email',
        'password',
        'group_id',
        'position_id',
        'process_id',
        'is_admin',
        'dispatch_flg',
        'part_flg',
        'always_order_flg',
        'fax_folder_name',
    ];
}
