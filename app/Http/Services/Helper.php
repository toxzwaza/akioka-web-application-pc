<?php

namespace App\Http\Services;

use App\Models\InitialOrder;
use App\Models\NotifyQueue;
use App\Models\NotifyQueueUser;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
    public static function createApprovalFlow($price, $user_id)
    {

        $user = User::find($user_id);
        if (!$user->group_id >= 8) {
            return [];
        }

        $approval_list = [];


        // 部署ごとの承認者マッピング
        $approvalMap = [
            2 => 16, // 梶谷課長
            3 => 37, // 長谷川課長
            4 => 84, // 宮原課長
            6 => 63, //常務
            7 => 36, //荒川部長
        ];

        // 基本の承認フロー
        $approval_list[] = $approvalMap[$user->group_id] ?? 2;


        // 10,000円以上の場合の追加承認
        if ($price >= 10000) {
            if ($user->group_id == 2) {
                $approval_list[] = 94;
            } else if (in_array($user->group_id, [3, 4, 5])) {
                $approval_list[] = 2;
            }
        }

        // 50,000円以上の場合の追加承認
        if ($price >= 100000) {
            if ($user->group_id == 7) {
                $approval_list[] = 63;
            }
        }

        // 150,000円以上の場合の追加承認
        if ($price >= 150000 && in_array($user->group_id, [1, 6, 7])) {
            $approval_list[] = 2;
        }

        return $approval_list;
    }
}
