<?php

namespace App\Http\Controllers;

use App\Models\OrderRequest;
use App\Models\Stock;
use App\Models\StockSupplier;
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
        $order_requests = OrderRequest::select('order_requests.id', 'order_requests.stock_id', 'order_requests.id as order_request_id','order_requests.accept_flg', 'stocks.img_path','stocks.name','stocks.s_name', 'stocks.price as stock_price' ,'stocks.url', 'order_requests.quantity', 'order_requests.created_at', 'users.name as request_user_name')->join('stocks', 'stocks.id',  'order_requests.stock_id')->leftJoin('users','users.id','order_requests.request_user_id')->where('status', 0)->get();

        foreach($order_requests as $order_request){
           $stock_supplier = StockSupplier::select('suppliers.id as supplier_id','suppliers.name as supplier_name','stock_suppliers.lead_time')->join('suppliers', 'suppliers.id', 'stock_suppliers.supplier_id')->where('stock_id', $order_request->stock_id)->where('stock_suppliers.del_flg', 0)->first();
           $order_request->stock_supplier = $stock_supplier;
        }

        return response()->json($order_requests);
    }
    public function completeOrderRequest(Request $request){
        $status = true;
        $order_request_id = $request->order_request_id;
        $user_id = $request->user_id;
        $quantity = $request->quantity;
        $price = $request->price;

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
        $order_request->price = $price;
        $order_request->save();

        return response()->json($status);
    }
}
