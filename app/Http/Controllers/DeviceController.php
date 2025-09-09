<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeviceController extends Controller
{
    /**
     * デバイス情報一覧画面を表示
     */
    public function index()
    {
        return Inertia::render('Master/Device/Index');
    }

    /**
     * デバイス情報一覧のAPIエンドポイント
     */
    public function getDevices(Request $request)
    {
        $query = Device::query();

        // 検索機能
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        // ステータスフィルタ
        if ($request->has('status_filter') && !empty($request->status_filter)) {
            $statusFilter = $request->status_filter;
             $query->whereRaw("
                 CASE 
                     WHEN last_access_date IS NULL OR last_access_date = '0000-00-00 00:00:00' OR YEAR(last_access_date) < 1900 THEN 'offline'
                     WHEN TIMESTAMPDIFF(HOUR, last_access_date, NOW()) <= 24 THEN 'online'
                     WHEN TIMESTAMPDIFF(HOUR, last_access_date, NOW()) <= 168 THEN 'warning'
                     ELSE 'offline'
                 END = ?
             ", [$statusFilter]);
        }

        $devices = $query->orderByRaw('
                CASE 
                    WHEN last_access_date IS NULL OR last_access_date = "0000-00-00 00:00:00" OR YEAR(last_access_date) < 1900 THEN 0
                    ELSE 1
                END DESC,
                last_access_date DESC,
                name ASC
            ')
            ->paginate(20)
            ->through(function ($device) {
                return [
                    'id' => $device->id,
                    'name' => $device->name,
                    'device_type' => $device->device_type,
                    'ip_address' => $device->ip_address,
                    'mac_address' => $device->mac_address,
                    'location' => $device->location,
                    'status' => $device->status,
                     'last_access_date' => $device->hasValidLastAccessDate() ? $device->last_access_date->format('Y-m-d H:i:s') : null,
                     'last_access_date_formatted' => $device->hasValidLastAccessDate() ? $device->last_access_date->format('Y年m月d日 H:i') : '未接続',
                    'connection_status' => $device->connection_status,
                    'status_color' => $device->status_color,
                    'description' => $device->description,
                    'created_at' => $device->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $device->updated_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json($devices);
    }

    /**
     * デバイス情報作成画面
     */
    public function create()
    {
        return Inertia::render('Master/Device/Create');
    }

    /**
     * デバイス情報保存
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device_type' => 'required|string|max:100',
            'ip_address' => 'nullable|ip',
            'mac_address' => 'nullable|string|max:17',
            'location' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            Device::create($request->all());
            
            return response()->json([
                'status' => true,
                'message' => 'デバイス情報を登録しました。'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'デバイス情報の登録に失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * デバイス情報編集画面
     */
    public function edit(Device $device)
    {
        return Inertia::render('Master/Device/Edit', [
            'device' => $device
        ]);
    }

    /**
     * デバイス情報更新
     */
    public function update(Request $request, Device $device)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device_type' => 'required|string|max:100',
            'ip_address' => 'nullable|ip',
            'mac_address' => 'nullable|string|max:17',
            'location' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $device->update($request->all());
            
            return response()->json([
                'status' => true,
                'message' => 'デバイス情報を更新しました。'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'デバイス情報の更新に失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * デバイス情報削除
     */
    public function destroy(Device $device)
    {
        try {
            $device->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'デバイス情報を削除しました。'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'デバイス情報の削除に失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * デバイスの最終アクセス時間を更新（API用）
     */
    public function updateLastAccess(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
        ]);

        try {
            $device = Device::findOrFail($request->device_id);
            $device->update([
                'last_access_date' => now()
            ]);
            
            return response()->json([
                'status' => true,
                'message' => '最終アクセス時間を更新しました。'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => '最終アクセス時間の更新に失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }
}
