<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $devices = [
            [
                'name' => 'オフィスPC-001',
                'device_type' => 'PC',
                'ip_address' => '192.168.1.100',
                'mac_address' => '00:11:22:33:44:55',
                'location' => 'オフィス1階 営業部',
                'status' => '稼働中',
                'last_access_date' => Carbon::now()->subMinutes(30), // 30分前
                'description' => '営業部メイン PC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'タブレット-001',
                'device_type' => 'タブレット',
                'ip_address' => '192.168.1.101',
                'mac_address' => '00:11:22:33:44:56',
                'location' => '工場 製造ライン1',
                'status' => '稼働中',
                'last_access_date' => Carbon::now()->subHours(2), // 2時間前
                'description' => '製造ライン用タブレット',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'プリンター-001',
                'device_type' => 'プリンター',
                'ip_address' => '192.168.1.200',
                'mac_address' => '00:11:22:33:44:57',
                'location' => 'オフィス1階 共用スペース',
                'status' => '稼働中',
                'last_access_date' => Carbon::now()->subDays(3), // 3日前
                'description' => 'レーザープリンター',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ルーター-001',
                'device_type' => 'ルーター',
                'ip_address' => '192.168.1.1',
                'mac_address' => '00:11:22:33:44:58',
                'location' => 'サーバールーム',
                'status' => '稼働中',
                'last_access_date' => Carbon::now()->subHours(1), // 1時間前
                'description' => 'メインルーター',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'サーバー-001',
                'device_type' => 'サーバー',
                'ip_address' => '192.168.1.10',
                'mac_address' => '00:11:22:33:44:59',
                'location' => 'サーバールーム',
                'status' => '稼働中',
                'last_access_date' => Carbon::now()->subMinutes(5), // 5分前
                'description' => 'Webサーバー',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '古いPC-001',
                'device_type' => 'PC',
                'ip_address' => '192.168.1.150',
                'mac_address' => '00:11:22:33:44:60',
                'location' => '倉庫',
                'status' => '停止中',
                'last_access_date' => Carbon::now()->subDays(10), // 10日前
                'description' => '廃止予定のPC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($devices as $device) {
            Device::create($device);
        }
    }
}
