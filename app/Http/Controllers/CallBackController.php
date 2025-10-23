<?php

namespace App\Http\Controllers;

use App\Models\InitialOrder;
use App\Models\NotifyQueue;
use App\Models\NotifyQueueUser;
use Exception;
use Illuminate\Http\Request;

class CallBackController extends Controller
{
    //
    public function callback(Request $request)
    {
        $status = true;
        $msg = '';

        $flg = $request->flg;
        $value = $request->value;

        try {
            if ($flg == 'initial_order_id') {
                $initial_order = InitialOrder::find($value);
                $notify_queue = new NotifyQueue();
                $notify_queue->title = 'FAX送信完了';
                $notify_queue->msg = '品名: ' . $initial_order->name . "\n" .
                                     '品番: ' . $initial_order->s_name . "\n" .
                                     $initial_order->s_name . "\n" .
                                     '発注先: ' . $initial_order->com_name . "\n" .
                                     "のFAX送信が完了しました。";
                $notify_queue->save();

                $notify_queue_user = new NotifyQueueUser();
                $notify_queue_user->notify_queue_id = $notify_queue->id;
                $notify_queue_user->user_id = $initial_order->user_id;
                $notify_queue_user->save();
            }
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'msg' => $msg]);
    }
}
