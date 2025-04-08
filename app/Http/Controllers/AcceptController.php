<?php

namespace App\Http\Controllers;

use App\Models\OrderRequest;
use Exception;
use Illuminate\Http\Request;
use App\Http\Services\Helper;
use App\Models\User;
use Inertia\Inertia;

class AcceptController extends Controller
{
    public function index(Request $request)
    {
        $order_request_id = $request->order_request_id;

        // 承認用画面
        $order_request = OrderRequest::select(
            'order_requests.id as order_request_id',
            'order_requests.quantity',
            'order_requests.price',
            'order_requests.user_id',
            'stocks.name as stock_name',
            'stocks.s_name as stock_s_name',
            'stocks.img_path as stock_img_path',
            'suppliers.name as supplier_name',
            'suppliers.tel as supplier_tel',
            'suppliers.fax as supplier_fax',
            'users.name as user_name',
            'request_user.name as request_user_name'
        )
            ->join('stocks', 'stocks.id', '=', 'order_requests.stock_id')
            ->join('suppliers', 'suppliers.id', '=', 'order_requests.supplier_id')
            ->join('users', 'users.id', '=', 'order_requests.user_id')
            ->leftJoin('users as request_user', 'request_user.id', '=', 'order_requests.request_user_id')
            ->where('order_requests.id', $order_request_id)
            ->first();

        return Inertia::render('Stock/Accept/Index', ['order_request' => $order_request]);
    }

    // 承認依頼送信
    public function sendAccept(Request $request)
    {
        $status = true;
        $msg = "";

        try {
            $order_request_id = $request->order_request_id;
            $order_request = OrderRequest::find($order_request_id);
            $order_request->accept_flg = 1;
            $order_request->save();

            $notify_users = [];
            $url = "https://akioka.cloud/accept/order-request?user_id=";

            if($order_request->new_stock_flg){
                // 新規品の場合は常務承認
                // array_push($notify_users, 63);
                array_push($notify_users, 91);
                $url .= "91";
            }else{
                // 既存品は部長承認
                // array_push($notify_users, 36);
                array_push($notify_users, 91);
                $url .= "91";
            }

            $title = "在庫管理システムからの通知です。";
            $message = "承認依頼を受け付けました。\n\n以下のURLから承認を行ってください。";
            

            Helper::createNotifyQueue($title, $message, $url, $notify_users);

        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }


        return response()->json(['status' => $status, 'msg' => $msg]);
    }

    public function store(Request $request)
    {
        $status = true;
        $order_request_id = $request->order_request_id;
        $accept_flg = $request->accept_flg;



        try {
            if($accept_flg){
                $order_request = OrderRequest::find($order_request_id);
                $order_request->accept_flg = $accept_flg;
                $order_request->save();
            }

            $order_request = OrderRequest::select('order_requests.accept_flg', 'stocks.name as stock_name', 'stocks.s_name as stock_s_name')->join('stocks', 'stocks.id', '=', 'order_requests.stock_id')
                ->where('order_requests.id', $order_request_id)
                ->first();

            // 通知者リスト
            $notify_list = ['村上飛羽','三谷優月', '岡堂莉子', '中村仁美'];

            if ($accept_flg == 2) {
                $message = $order_request->stock_name . ' ' . $order_request->stock_s_name . "の発注依頼が承認されました。\n\n以下のURLから発注を行ってください。";
            } else if ($accept_flg == 3) {
                $message = "承認が拒否されました。";
            }

            foreach ($notify_list as $notify) {
                $user = User::where('name', $notify)->first();
                Helper::sendNotify([$user->email], $message, route('stock.order_requests', ['user_id' => $user->id]));
            }
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }
}
