<?php

namespace App\Http\Controllers;

use App\Models\OrderRequest;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderRequestController extends Controller
{
    //
        //
    // 発注依頼一覧
    public function index(Request $request)
    {
        $user_id = $request->user_id ?? null;

        // 発注可能ユーザー（総務部）
        $order_users = User::select('users.id', 'users.name')->join('groups', 'users.group_id', 'groups.id')->where('groups.id', 7)->get();

        return Inertia::render('Stock/OrderRequests', ['order_users' => $order_users, 'user_id' => $user_id]);
    }

    public function getOrderRequests()
    {
        // 未受理の注文依頼のみ取得
        $order_requests = OrderRequest::select('order_requests.stock_id', 'order_requests.id as order_request_id', 'stocks.img_path','stocks.name','stocks.s_name', 'stocks.url', 'order_requests.quantity', 'order_requests.created_at')->join('stocks', 'stocks.id',  'order_requests.stock_id')->where('status', 0)->get();

        return response()->json($order_requests);
    }
    public function completeOrderRequest(Request $request){
        $status = true;
        $order_request_id = $request->order_request_id;
        $user_id = $request->user_id;
        $quantity = $request->quantity;

        if(!$order_request_id){
            $status = false;
        }

        $order_request = OrderRequest::find($order_request_id);
        $stock = Stock::find($order_request->stock_id);

        // ネット注文品以外はdel_flgを立てる
        if(!$stock->url){
            $order_request->del_flg = 1;
        }

        $order_request->status = 1;
        $order_request->user_id = $user_id;
        $order_request->quantity = $quantity;
        $order_request->save();

        return response()->json($status);
    }
}
