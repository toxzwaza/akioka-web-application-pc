<?php

namespace App\Http\Controllers;

use App\Models\OrderRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseOrder extends Controller
{
    //
    public function index(Request $request, $order_request_id)
    {
        $order_request = OrderRequest::find($order_request_id);

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
                'users.name as user_name'
            )
            ->join('stocks', 'stocks.id', '=', 'order_requests.stock_id')
            ->join('suppliers', 'suppliers.id', '=', 'order_requests.supplier_id')
            ->join('users', 'users.id', '=', 'order_requests.user_id')
            ->where('order_requests.id', $order_request_id)
            ->first();
        }


        return Inertia::render('Stock/Stocks/PurchaseOrder', [
            'order_request' => $order_request
        ]);
    }
}
