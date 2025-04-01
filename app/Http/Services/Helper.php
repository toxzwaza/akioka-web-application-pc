<?php

namespace App\Http\Services;

use App\Models\InitialOrder;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Helper
{
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
    public static function createOrderNo(){
        $nextCount = InitialOrder::whereDate('created_at', now()->toDateString())->count();

        // Sはシステムの略（重複しないようにする為に設定。発注がシステムで統一された頃にS-を排除する）
        
        $order_no = 'S-' . now()->format('y-m-') . ($nextCount + 1);

        return $order_no;
    }
}
