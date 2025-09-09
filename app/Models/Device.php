<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'device_type',
        'ip_address',
        'mac_address',
        'location',
        'status',
        'last_access_date',
        'description'
    ];

    protected $casts = [
        'last_access_date' => 'datetime'
    ];

    /**
     * 有効な最終アクセス日時かどうかを判定
     */
    public function hasValidLastAccessDate()
    {
        return $this->last_access_date && 
               $this->last_access_date->year >= 1900 && 
               $this->last_access_date->format('Y-m-d H:i:s') !== '0000-00-00 00:00:00';
    }

    /**
     * デバイスの接続状態を取得
     */
    public function getConnectionStatusAttribute()
    {
        if (!$this->hasValidLastAccessDate()) {
            return 'offline';
        }

        $hoursAgo = now()->diffInHours($this->last_access_date);
        
        if ($hoursAgo <= 24) {
            return 'online';
        } elseif ($hoursAgo <= 168) { // 1週間 = 168時間
            return 'warning';
        } else {
            return 'offline';
        }
    }

    /**
     * デバイスの接続状態の色を取得
     */
    public function getStatusColorAttribute()
    {
        switch ($this->connection_status) {
            case 'online':
                return 'text-green-500';
            case 'warning':
                return 'text-orange-500';
            default:
                return 'text-red-500';
        }
    }
}
