<?php

namespace App\Http\Controllers;

use App\Models\LunchOrder;
use App\Models\LunchOrderArchive;
use App\Models\TodayLunchDescription;
use App\Services\Method;
use Illuminate\Http\Request;

class LunchController extends Controller
{
    //
    public function index()
    {

        return view('master.index');
    }

    public function order_archive(Request $request)
    {
        $orders = LunchOrderArchive::select('lunch_order_archives.*', 'users.name as user_name')->join('users', 'users.id', 'lunch_order_archives.user_id')->orderby('id', 'desc')->paginate(20);

        return view('lunch.order-archive', compact('orders'));
    }

    public function order_users(Request $request)
    {
        $date = $request->date;

        $order_users = LunchOrder::select('lunch_orders.*', 'users.name as user_name', 'lunches.name as lunch_name')->join('users', 'users.id', 'lunch_orders.user_id')->join('lunches', 'lunches.id', 'lunch_orders.lunch_id')->whereDate('lunch_orders.created_at', $date)->where('order_flg', 1)->distinct('users.id')->get();
        $user_count = count($order_users);

        return view('lunch.order-users', compact('order_users', 'user_count'));
    }

    public function create_description(Request $request)
    {
        $today_lunch_description = TodayLunchDescription::select('description')->whereDate('created_at', \Carbon\Carbon::today()->format('Y-m-d'))->first();

        return view('lunch.create_description', compact('today_lunch_description'));
    }
    public function store_description(Request $request)
    {
        $description = $request->description;
        $method = $request->method;
        switch ($method) {
            case 'delete':
                $today_lunch_description = TodayLunchDescription::whereDate('created_at',\Carbon\Carbon::today()->format('Y-m-d'))->first();
             

                if($today_lunch_description){
                    $today_lunch_description->delete();
          
                    Method::msg('success','本日の備考を削除しました。');
                }

                break;
            default:
                if (!$description) {
                    Method::msg('error', '備考が入力されていません。');
                    return redirect()->back();
                }

                $today_lunch_description = new TodayLunchDescription();
                $today_lunch_description->description = $description;
                $today_lunch_description->save();

                Method::msg('success', '本日の備考を追加しました');


                break;
        }


        return redirect()->back();
    }
}
