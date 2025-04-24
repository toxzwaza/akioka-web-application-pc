<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Lunch;
use App\Models\LunchOrder;
use App\Models\LunchOrderArchive;
use App\Models\TodayLunchDescription;
use App\Models\User;
use App\Services\Method;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LunchController extends Controller
{
    //
    public function index()
    {
        return redirect()->route('lunch.order');
        return Inertia::render('Lunch/Index');
    }
    public function order()
    {

        $price = Lunch::find(1)->price;
        $lunch_order = LunchOrder::whereDate('date', \Carbon\Carbon::today()->format('Y-m-d'))->where('order_flg', 1)->get();
        $today_lunch_description = TodayLunchDescription::whereDate('created_at', \Carbon\Carbon::today()->format('Y-m-d'))->first();

        $count = count($lunch_order);

        // 注文書アーカイブが存在しない場合
        $lunch_order_archive = LunchOrderArchive::whereDate('created_at', \Carbon\Carbon::today()->format('Y-m-d'))->first();
        if (!$lunch_order_archive) {
            $lunch_order_archive = new LunchOrderArchive();
            $lunch_order_archive->lunch_count = $count;
            $lunch_order_archive->side_dish_count = 0;
            $lunch_order_archive->lunch_calc = $count * $price;
            $lunch_order_archive->side_dish_calc = 0;
            $lunch_order_archive->memo = $today_lunch_description->description ?? '';
            $lunch_order_archive->user_id = 117;
            $lunch_order_archive->save();
        }


        return Inertia::render('Lunch/Order', ['count' => $count, 'price' => $price, 'today_lunch_description' => $today_lunch_description->description ?? '']);
    }

    public function reserve(Request $request)
    {

        $users = User::select('id', 'name', 'group_id')->where('del_flg', 0)->get();
        $groups = Group::get();

        // 明日以降の弁当注文データを取得
        $lunch_orders = LunchOrder::select('lunch_orders.id', 'lunch_orders.created_at', 'lunch_orders.date', 'users.name as user_name')->join('users', 'users.id', 'lunch_orders.user_id')->whereDate('date', Carbon::tomorrow()->format('Y-m-d'))->where('order_flg', 1)->get();

        return Inertia::render('Lunch/Reserve', ['users' => $users, 'groups' => $groups, 'lunch_orders' => $lunch_orders]);
    }

    public function reserve_store(Request $request)
    {

        $status = true;

        $user_id = $request->user_id;
        $date = $request->date;


        try {
            $lunch_order = new LunchOrder();
            $lunch_order->lunch_id = 1;
            $lunch_order->user_id = $user_id;
            $lunch_order->date = $date;
            $lunch_order->order_flg = 1;
            $lunch_order->save();
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }
    public function reserve_delete(Request $request)
    {

        $status = true;

        $lunch_order_id = $request->lunch_order_id;

        try {
            $lunch_order = LunchOrder::find($lunch_order_id);
            if ($lunch_order) {
                $lunch_order->delete();
            }
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
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
                $today_lunch_description = TodayLunchDescription::whereDate('created_at', \Carbon\Carbon::today()->format('Y-m-d'))->first();


                if ($today_lunch_description) {
                    $today_lunch_description->delete();

                    Method::msg('success', '本日の備考を削除しました。');
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


    public function getMonthOrders()
    {
        $orders = LunchOrderArchive::selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as created_date, lunch_count, side_dish_count')->get();
        $orders = response()->json($orders);

        return $orders;
    }
    
    public function export_csv(){

        return Inertia::render('Lunch/ExportCsv');
    }

    public function export(Request $request)
    {
        $start_date = $request->start_date;
        $finish_date = $request->finish_date;

        $monthOrders = LunchOrder::select('user_id', 'users.name as user_name', 'users.dispatch_flg', 'users.part_flg')
            ->join('users', 'users.id', 'lunch_orders.user_id')
            ->distinct()
            ->whereDate('date', '>=', $start_date)
            ->whereDate('date', '<=', $finish_date)
            ->orderBy('dispatch_flg', 'asc')
            ->orderby('part_flg', 'asc')
            ->orderby('users.id', 'asc')
            ->get();

        $calc_lunch_count = 0;
        $calc_side_dish_count = 0;

        foreach ($monthOrders as $monthOrder) {
            $lunch_count = LunchOrder::whereDate('date', '>=', $start_date)
                ->whereDate('date', '<=', $finish_date)
                ->where('user_id', $monthOrder->user_id)
                ->where('lunch_id', '1')
                ->where('order_flg', '1')
                ->count();



            $monthOrder->lunch_count = $lunch_count;
            $monthOrder->calc = ($lunch_count * 360);

            if (!$monthOrder->dispatch_flg) {
                $calc_lunch_count += $lunch_count;
            }

            if ($monthOrder->dispatch_flg) {
                $monthOrder->attribute = '派遣・非常勤';
            } else if ($monthOrder->part_flg) {
                $monthOrder->attribute = "パート社員";
            } else {
                $monthOrder->attribute = "正社員";
            }
        }

        $fileName = "弁当注文集計_{$start_date}_{$finish_date}.csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma" => "no-cache", 
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['名前', '社員属性', '弁当個数', '合計金額'];
        $callback = function() use ($monthOrders, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOMを追加
            fputcsv($file, $columns);
            
            foreach ($monthOrders as $monthOrder) {
                fputcsv($file, [
                    $monthOrder->user_name,
                    $monthOrder->attribute,
                    $monthOrder->lunch_count,
                    '¥' . number_format($monthOrder->calc)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
