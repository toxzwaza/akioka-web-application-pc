<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\OrderRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseOrder extends Controller
{
    //
    public function index(Request $request, $order_request_id)
    {
        $order_request = OrderRequest::find($order_request_id);
        // 今月と来月のカレンダー情報取得
        $current_month_holidays = Holiday::select('date')
            ->where('date', '>=', now()->startOfMonth())
            ->where('date', '<=', now()->endOfMonth())
            ->where('is_holiday', 1)
            ->get();

        $next_month_holidays = Holiday::select('date')
            ->where('date', '>=', now()->addMonth()->startOfMonth())
            ->where('date', '<=', now()->addMonth()->endOfMonth())
            ->where('is_holiday', 1)
            ->get();

        if ($order_request) {
            $order_request->update([
                'user_id' => $request->user_id,
                'supplier_id' => $request->supplier_id,
                'price' => $request->price,
                'quantity' => $request->quantity,
            ]);

            $order_request = OrderRequest::select(
                'order_requests.id as order_request_id',
                'order_requests.quantity',
                'order_requests.price',
                'order_requests.user_id',
                'stocks.name as stock_name',
                'stocks.s_name as stock_s_name',
                'suppliers.name as supplier_name',
                'suppliers.tel as supplier_tel',
                'suppliers.fax as supplier_fax',
                'users.name as user_name',
                'request_user.name as request_user_name',
                'stocks.deli_location'
            )
            ->join('stocks', 'stocks.id', '=', 'order_requests.stock_id')
            ->join('suppliers', 'suppliers.id', '=', 'order_requests.supplier_id')
            ->join('users', 'users.id', '=', 'order_requests.user_id')
            ->leftJoin('users as request_user', 'request_user.id', '=', 'order_requests.request_user_id')
            ->where('order_requests.id', $order_request_id)
            ->first();
        }


        return Inertia::render('Stock/Stocks/PurchaseOrder', [
            'order_request' => $order_request,
            'current_month_holidays' => $current_month_holidays,
            'next_month_holidays' => $next_month_holidays,
        ]);
    }
}
