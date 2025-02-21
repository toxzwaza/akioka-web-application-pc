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
    //
    public function sendAccept(Request $request)
    {
        $status = true;
        try {
            $order_request_id = $request->order_request_id;
            $order_request = OrderRequest::find($order_request_id);
            $order_request->accept_flg = 1;
            $order_request->save();

            $message = "承認依頼を受け付けました。\n\n以下のURLから承認を行ってください。";

            $url = route('stock.accept', ['order_request_id' => $order_request_id]);

            Helper::sendNotify(['ka-arakawa@akioka-ltd.jp'], $message, $url);
        } catch (Exception $e) {
            $status = false;
        }


        return response()->json(['status' => $status]);
    }

    public function store(Request $request)
    {
        $status = true;
        $order_request_id = $request->order_request_id;
        $accept_flg = $request->accept_flg;

        try {
            $order_request = OrderRequest::select('order_requests.accept_flg', 'stocks.name as stock_name', 'stocks.s_name as stock_s_name')->join('stocks', 'stocks.id', '=', 'order_requests.stock_id')
                ->where('order_requests.id', $order_request_id)
                ->first();
            $order_request->accept_flg = $accept_flg;
            $order_request->save();

            // 通知者リスト
            $notify_list = ['村上飛羽', '三谷優月', '岡堂莉子', '中村仁美'];
            $url = route('stock.order_requests');

            if ($accept_flg == 2) {
                $message = $order_request->stock_name . ' ' . $order_request->stock_s_name . "の発注依頼が承認されました。\n\n以下のURLから発注を行ってください。";
            } else if ($accept_flg == 3) {
                $message = "承認が拒否されました。";
            }

            foreach ($notify_list as $notify) {
                $user = User::where('name', $notify)->first();
                Helper::sendNotify([$user->email], $message, $url);
            }
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }
}
