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
    public static function createApprovalFlow($price, $user_id, $new_flg = 0)
    {

        $user = User::find($user_id);
        if (in_array($user->group_id, [8, 10, 11]) ||
        !$new_flg && !in_array($user->group_id, [3, 4]) ||
        !$new_flg && in_array($user->group_id, [3,4]) && $user->position_id == 6
        ) { //役員・統括部（役員）・顧問 または 既存品の製造1,2以外、既存品製造１２でも課長は承認なし
            return [];
        }

        $approval_list = [];


        if ($user->position_id >= 7) {  //係長・GL・一般 からの依頼の場合
            // 部署ごとの承認者マッピング
            $approvalMap = [
                1 => 63, // 技術 常務
                2 => 16, // 品証 梶谷課長
                3 => 37, // 製造一課 長谷川課長
                4 => 84, // 製造二課 宮原課長
                5 => 2,  // 保全 社長
                6 => 63, // 業務 常務
                7 => 36, // 総務 荒川部長
            ];

            // ユーザーの部署に対応する承認者を追加
            if (isset($approvalMap[$user->group_id])) {
                $approval_list[] = $approvalMap[$user->group_id];
            }

            // 10,000円以上の場合の追加承認
            if ($price >= 10000) {
                if (in_array($user->group_id, [2])) {
                    $approval_list[] = 94; //品証 => 細矢本部長
                } else if (in_array($user->group_id, [3, 4])) {
                    $approval_list[] = 2; //製造1,2 => 社長
                }
            }

            // 150,000円以上の場合の追加承認
            if ($price >= 150000) {
                if (in_array($user->group_id, [1, 2, 6, 7])) {
                    $approval_list[] = 2;
                }
            }
        } else if ($user->position_id == 6) { //課長からの依頼
            // 部署ごとの承認者マッピング
            $approvalMap = [
                2 => 94, // 品証 梶谷課長
                3 => 2,  // 製造一課 長谷川課長
                4 => 2,  // 製造二課 宮原課長
            ];

            // ユーザーの部署に対応する承認者を追加
            if (isset($approvalMap[$user->group_id])) {
                $approval_list[] = $approvalMap[$user->group_id];
            }

            // 150,000円以上の場合の追加承認
            if ($price >= 150000) {
                if (in_array($user->group_id, [2])) {
                    $approval_list[] = 2;
                }
            }
        }

        return $approval_list;
    }
}
