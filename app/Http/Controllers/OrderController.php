<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\ObjectRequest;
use App\Models\RequestAcceptFlow;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    public function index()
    {

        return view('order.index');
    }

    public function already_orders()
    {
        // 稟議取得
        $approvals = Approval::select('approvals.*', 'users.name as user_name')->join('users', 'users.id', 'approvals.user_id')
            ->where('status', '=', 1)->where('order_status', '=', 0)->get();



        // 物品依頼
        $object_requests = ObjectRequest::select('object_requests.*', 'stocks.name as stock_name', 'stocks.img_path', 'stocks.price', 'stocks.url', 'users.name as user_name')->join('stocks', 'stocks.id', 'object_requests.stock_id')->join('users', 'users.id', 'object_requests.user_id')->where('status', '=', 1)->where('order_status', '=', 0)->get();
        // 承認フローが承認されていない場合、トリム
        foreach ($object_requests as $key => $object_request) {

            $flows = RequestAcceptFlow::where('request_id', '=', $object_request->id)->get();
            $is_valid = true;
            foreach ($flows as $flow) {
                if ($flow->status !== 1) {
                    $is_valid = false;
                    break;
                }
            }
            if (!$is_valid) {
                unset($object_requests[$key]);
            }
        }

        return view('order.already_orders', compact('approvals','object_requests'));
    }

    public function create_orders()
    {
    }
}
