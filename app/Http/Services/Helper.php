<?php

namespace App\Http\Services;

use App\Models\Device;
use App\Models\DeviceMessage;
use App\Models\InitialOrder;
use App\Models\NotifyQueue;
use App\Models\NotifyQueueUser;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Auth\Credentials\ServiceAccountCredentials;

class Helper
{
    public static function createNotifyQueue($title, $msg, $url, $users)
    {
        $status = true;

        try {
            DB::beginTransaction();
            try {
                $notifyQueue = new NotifyQueue();
                $notifyQueue->title = $title;
                $notifyQueue->msg = $msg;
                $notifyQueue->url = $url;
                $notifyQueue->save();

                foreach ($users as $user) {
                    $notifyQueueUser = new NotifyQueueUser();
                    $notifyQueueUser->notify_queue_id = $notifyQueue->id;
                    $notifyQueueUser->user_id = $user;
                    $notifyQueueUser->save();
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            $status = false;
        }

        return $status;
    }

    // Teamsより通知
    public static function sendNotify($notify_users, $message)
    {

        $client = new Client();
        $url = 'https://akioka.cloud/api/sendMessage';

        $response = $client->post($url, [
            'form_params' => [
                'notify_users' => $notify_users,
                'message' => $message
            ]
        ]);

        return $response;
    }

    // 注文No作成
    public static function createOrderNo()
    {
        $nextCount = InitialOrder::whereDate('created_at', now()->toDateString())->count();

        // Sはシステムの略（重複しないようにする為に設定。発注がシステムで統一された頃にS-を排除する）

        $order_no = now()->format('ymd-') . ($nextCount + 1);

        return $order_no;
    }

    // 承認フローを作成
    public static function createApprovalFlow($price, $user_id, $new_flg = 0)
    {

        $user = User::find($user_id);
        if (
            in_array($user->group_id, [8, 10, 11]) || //役員・統括部（役員）・顧問
            !$new_flg && in_array($user->group_id, [1, 2])  //既存品の品証・技術
            // || !$new_flg && in_array($user->group_id, [3, 4]) && $user->position_id == 6 //既存品製造部課長
        ) {
            return [];
        }

        $approval_list = [];


        if ($user->position_id >= 7) {  //係長・GL・一般 からの依頼の場合
            // 係長・GL・一般からの承認者マッピング
            $approvalMap = [
                1 => 63, // 技術 常務
                2 => 16, // 品証 梶谷課長
                3 => 37, // 製造一課 長谷川課長
                4 => 84, // 製造二課 宮原課長
                5 => 94, // 保全 細矢本部長
                6 => $new_flg ? 63 : 36, // 新規品: 常務 , 既存品: 部長
                7 => $new_flg ? 63 : 36, // 新規品: 常務 , 既存品: 部長
            ];

            // ユーザーの部署に対応する承認者を追加
            if (isset($approvalMap[$user->group_id])) {
                $approval_list[] = $approvalMap[$user->group_id];
            }

            // 10,000円以上の場合の追加承認
            if ($price > 10000) {
                if (in_array($user->group_id, [2, 3, 4])) {
                    $approval_list[] = 94; //品証 => 細矢本部長
                }
            }

            // 総務・業務の既存品は100000円を超えた場合常務承認
            if (in_array($user->group_id, [6, 7]) && !$new_flg && $price > 100000) {
                $approval_list[] = 63;
            }

            // 150,000円以上の場合の追加承認
            if ($price > 150000) {
                $approval_list[] = 2;
            }
        } else if ($user->position_id == 6) { //課長からの依頼
            // 部署ごとの承認者マッピング
            $approvalMap = [
                2 => 94, // 品証 梶谷課長=>細矢本部長
                3 => 94,  // 製造一課 長谷川課長=>細矢本部長
                4 => 94,  // 製造二課 宮原課長=>細矢本部長
            ];

            // ユーザーの部署に対応する承認者を追加
            if (isset($approvalMap[$user->group_id])) {
                $approval_list[] = $approvalMap[$user->group_id];
            }

            // 150,000円以上の場合の追加承認
            if ($price > 150000) {
                $approval_list[] = 2;
            }
        } else if ($user->position_id == 3) { //本部長からの依頼

            $approval_list[] = 2; //社長のみ
        } else if ($user->position_id == 5) { //荒川部長からの依頼

            $approval_list[] = 63; // 常務

            // 150,000円以上の場合の追加承認
            if ($price > 150000) {
                $approval_list[] = 2; // 社長
            }
        }

        return $approval_list;
    }


    public static function createDeviceMessage($priority, $to_device_id, $from_device_id = null, $to_user_id, $from_user_id, $msg)
    {
        $message_id = 0;

        if ($to_device_id) {
            $message = new DeviceMessage();
            $message->priority = $priority;
            $message->to_device_id = $to_device_id;
            $message->from_device_id = $from_device_id;
            $message->to_user_id = $to_user_id;
            $message->from_user_id = $from_user_id;
            $message->del_flg = 0;
            $message->message = $msg;
            $message->save();

            $message_id = $message->id;
        }

        return $message_id;
    }
}
