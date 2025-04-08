<?php

namespace App\Http\Controllers;

use App\Http\Services\Helper;
use App\Models\InitialOrder;
use App\Models\OrderRequest;
use App\Models\Stock;
use App\Models\StockSupplier;
use App\Models\Supplier;
use App\Models\User;
use Exception;
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
        $order_requests = OrderRequest::select('order_requests.id', 'order_requests.stock_id', 'order_requests.id as order_request_id', 'order_requests.accept_flg', 'stocks.img_path', 'stocks.name', 'stocks.s_name', 'stocks.price as stock_price', 'stocks.url', 'order_requests.quantity', 'order_requests.created_at', 'users.name as request_user_name', 'order_requests.postage', 'order_requests.calc_price')
            ->join('stocks', 'stocks.id',  'order_requests.stock_id')
            ->leftJoin('users', 'users.id', 'order_requests.request_user_id')->where('status', 0)
            ->where('order_requests.del_flg', 0)
            ->get();

        foreach ($order_requests as $order_request) {
            $stock_supplier = StockSupplier::select('suppliers.id as supplier_id', 'suppliers.name as supplier_name', 'stock_suppliers.lead_time')->join('suppliers', 'suppliers.id', 'stock_suppliers.supplier_id')->where('stock_id', $order_request->stock_id)->where('stock_suppliers.del_flg', 0)->first();
            $order_request->stock_supplier = $stock_supplier;
        }

        return response()->json($order_requests);
    }
    public function completeOrderRequest(Request $request)
    {
        $status = true;
        $order_request_id = $request->order_request_id;
        $user_id = $request->user_id;
        $quantity = $request->quantity;
        $price = $request->price;

        if (!$order_request_id) {
            $status = false;
        }

        $order_request = OrderRequest::find($order_request_id);
        $stock = Stock::find($order_request->stock_id);

        // ネット注文品以外はdel_flgを立てる
        if (!$stock->url) {
            $order_request->del_flg = 1;
        }

        $order_request->status = 1;
        $order_request->user_id = $user_id;
        $order_request->quantity = $quantity;
        $order_request->price = $price;
        $order_request->save();

        return response()->json($status);
    }

    public function delete(Request $request)
    {
        $order_request_id = $request->order_request_id;

        $status = true;

        try {
            $order_request = OrderRequest::find($order_request_id);
            $order_request->del_flg = 1;
            $order_request->save();
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }

    // 発注依頼から発注作成
    public function createInitialOrder(Request $request)
    {
        $order_request_id = $request->order_request_id;

        $order_request = OrderRequest::find($order_request_id);
        $supplier = Supplier::find($order_request->supplier_id);
        $stock = Stock::find($order_request->stock_id);

        $order_user_name = User::find($order_request->request_user_id)->name;

        $status = true;
        $msg = null;

        try {
            if ($order_request) {
                $initial_order = new InitialOrder();
                $initial_order->stock_id = $order_request->stock_id;
                $initial_order->order_no = Helper::createOrderNo();
                $initial_order->order_date = date('Y-m-d');
                $initial_order->com_no = $supplier->supplier_no;
                $initial_order->com_name = $supplier->name;
                $initial_order->name = $stock->name;
                $initial_order->s_name = $stock->s_name;
                $initial_order->order_unit = $stock->solo_unit;
                $initial_order->deli_location = $stock->deli_location;
                $initial_order->user_id = $order_request->user_id;
                $initial_order->order_user_id = $order_request->request_user_id;
                $initial_order->order_user = $order_user_name;
                $initial_order->supplier_id = $order_request->supplier_id;
                $initial_order->lead_time = $order_request->lead_time;
                $initial_order->price = $order_request->price;
                $initial_order->quantity = $order_request->quantity;
                $initial_order->calc_price = $order_request->calc_price;
                $initial_order->postage = $order_request->postage;
                $initial_order->expected_delivery_date = date('Y-m-d', strtotime('+' . $order_request->lead_time . ' days'));
                $initial_order->save();

                // 注文依頼完了
                $order_request->status = 1;
                $order_request->save();
            }
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'msg' => $msg]);
    }
}
