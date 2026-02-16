<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', // ここにuser_idを追加
        'supplier_id',
        'price',
        'quantity',
        'file_path_sub',
        // 他のフィールドも必要に応じて追加
    ];

    protected $casts = [
        'file_path_sub' => 'array',
    ];
    
}
