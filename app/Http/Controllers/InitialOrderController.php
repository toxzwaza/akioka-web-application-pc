<?php

namespace App\Http\Controllers;

use App\Http\Services\Helper;
use App\Models\Classification;
use App\Models\Group;
use App\Models\Holiday;
use App\Models\InitialOrder;
use App\Models\InventoryOperationRecord;
use App\Models\OrderRequest;
use App\Models\OrderRequestApproval;
use App\Models\Process;
use App\Models\Stock;
use App\Models\StockProcess;
use App\Models\StockStorage;
use App\Models\StockSupplier;
use App\Models\Supplier;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class InitialOrderController extends Controller
{
    //
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $order_by = $request->order_by ?? 'desc';
        $start_order_date = $request->start_order_date;
        $end_order_date = $request->end_order_date;
        $supplier_id = $request->supplier_id;
        $order_user_id = $request->order_user_id;
        $user_id = $request->user_id;
        $group_id = $request->group_id;
        $process_id = $request->process_id;
        $classification_id = $request->classification_id;

        $query = InitialOrder::select(
            'initial_orders.*',
            'stocks.img_path',
            'stocks.url',
            'stocks.classification_id',
            'stock_suppliers.lead_time as base_lead_time',
            'suppliers.tel',
            'suppliers.fax',
            'users.name as manage_user_name',
            'order_requests.id as order_request_id',
            'order_request_stock_processes.code as stock_processes_order_request_code',
            'order_request_stock_processes.name as stock_processes_order_request_name',
            'stock_processes_base.code as stock_processes_base_code',
            'stock_processes_base.name as stock_processes_base_name',
            'documents.id as document_id',
            'documents.title as document_title',
            'documents.content as document_content',
            'documents.main_reason as document_main_reason',
            'documents.sub_reason as document_sub_reason',
            'documents.evalution_date as document_evalution_date',
            'stocks.tax_included as stock_tax_included', //税区分
        )
            ->leftJoin('stocks', 'stocks.id', 'initial_orders.stock_id')
            ->leftJoin('order_requests', 'order_requests.id', 'initial_orders.order_request_id')
            ->leftJoin('stock_processes as order_request_stock_processes', 'order_request_stock_processes.id', 'order_requests.stock_process_id')
            ->leftJoin('stock_processes as stock_processes_base', 'stock_processes_base.id', 'stocks.stock_process_id')
            ->leftJoin('stock_suppliers', function ($join) {
                $join->on('stock_suppliers.stock_id', '=', 'initial_orders.stock_id')
                    ->on('stock_suppliers.supplier_id', '=', 'initial_orders.supplier_id');
            })
            ->leftJoin('suppliers', 'suppliers.id', 'initial_orders.supplier_id')
            ->leftJoin('users', 'users.id', 'initial_orders.user_id') //発注者
            ->leftJoin('users as order_users', 'order_users.id', 'initial_orders.order_user_id') //依頼者
            ->leftJoin('stock_processes', 'stock_processes.id', 'initial_orders.stock_process_id')
            ->leftJoin('documents', 'documents.id', 'order_requests.document_id')
            ->orderBy('initial_orders.created_at', 'desc');


        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('initial_orders.name', 'like', '%' . $keyword . '%')
                    ->orWhere('initial_orders.s_name', 'like', '%' . $keyword . '%');
            });
        }

        if ($start_order_date) {
            $query->where('initial_orders.order_date', '>=', $start_order_date);
        }

        if ($end_order_date) {
            $query->where('initial_orders.order_date', '<=', $end_order_date);
        }

        if ($supplier_id) {
            $query->where('initial_orders.supplier_id', $supplier_id);
        }

        if ($order_user_id) {
            $query->where('initial_orders.order_user_id', $order_user_id);
        }

        if ($user_id) {
            $query->where('initial_orders.user_id', $user_id);
        }

        if ($order_by) {
            $query->orderBy('initial_orders.order_date', $order_by);
        }

        if ($group_id) {
            $query->where('order_users.group_id', $group_id);
        }
        if ($process_id) {
            $query->where('order_users.process_id', $process_id);
        }
        if ($classification_id) {
            $query->where('stocks.classification_id', $classification_id);
        }

        $initial_orders = $query->where('initial_orders.del_flg', 0)->paginate(14)->withQueryString();

        // 承認者を取得
        foreach ($initial_orders as $initial_order) {
            $order_request_approvals = OrderRequestApproval::select(
                'order_request_approvals.created_at',
                'users.name as user_name',
            )
                ->join('users', 'users.id', 'order_request_approvals.user_id')
                ->where('order_request_id', $initial_order->order_request_id)->get();

            $initial_order->order_request_approvals = $order_request_approvals;
        }


        // 総合計発注数と金額、今月の発注数と金額を取得
        $totals = [
            'total_order_count' => $query->count(),
            'total_calc_price_sum' => $query->sum('initial_orders.calc_price'),
            'current_month_count' => InitialOrder::whereBetween('initial_orders.order_date', [now()->startOfMonth(), now()->endOfMonth()])->where('del_flg', 0)->count(),
            'current_month_sum' => InitialOrder::whereBetween('initial_orders.order_date', [now()->startOfMonth(), now()->endOfMonth()])->where('del_flg', 0)->sum('initial_orders.calc_price')
        ];

        // 発注書用 今月と来月のカレンダー情報取得
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


        $admin_users = User::select('id', 'name', 'password')->where('is_admin', 1)->get();


        // 注文先一覧を取得
        $suppliers = Supplier::select('suppliers.id', 'suppliers.name')->join('initial_orders', 'suppliers.id', 'initial_orders.supplier_id')
            ->distinct()
            ->get();

        // 発注依頼者一覧を取得
        $users = User::select('users.id', 'users.name')
            ->join('initial_orders', 'users.id', '=', 'initial_orders.user_id')
            ->distinct()
            ->get();

        // 発注担当者一覧を取得
        $order_users = User::select('users.id', 'users.name')
            ->join('initial_orders', 'users.id', '=', 'initial_orders.order_user_id')
            ->distinct()
            ->get();

        $groups = Group::select('id', 'name')
            ->get();
        $processes = Process::select('id', 'name')->get();
        $classifications = Classification::select('id', 'name')->get();




        return Inertia::render('Stock/InitialOrders', [
            'current_month_holidays' => $current_month_holidays,
            'next_month_holidays' => $next_month_holidays,
            'admin_users' => $admin_users,
            'initial_orders' => $initial_orders,
            'users' => $users,
            'order_users' => $order_users,
            'suppliers' => $suppliers,
            'totals' => $totals,
            'groups' => $groups,
            'processes' => $processes,
            'classifications' => $classifications
        ]);
    }


    public function create()
    {

        $classifications = Classification::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();
        $admin_users = User::select('id', 'name')->where('is_admin', 1)->get();
        $suppliers = Supplier::select('id', 'name', 'supplier_no')->get();
        $stock_processes = StockProcess::select('id', 'name')->get();

        return Inertia::render('Stock/Stocks/InitialOrder', ['classifications' => $classifications, 'users' => $users, 'admin_users' => $admin_users, 'suppliers' => $suppliers, 'stock_processes' => $stock_processes]);
    }


    public function store(Request $request)
    {
        $status = true;
        $msg = '';

        $stock_id = $request->stock_id;
        $name = $request->name;
        $s_name = $request->s_name;
        $jan_code = $request->jan_code;
        $img_path = $request->img_path;
        $url = $request->url;
        $purchase_identification_number = $request->purchase_identification_number;
        $price = $request->price;
        $solo_unit = $request->solo_unit;
        $org_unit = $request->org_unit;
        $quantity_per_org = $request->quantity_per_org;
        $classification_id = $request->classification_id;
        $deli_location = $request->deli_location;
        $base_stock_process_id = $request->base_stock_process_id;
        $order_stock_process_id = $request->order_stock_process_id;

        $order_user = $request->order_user;
        $user_id = $request->user_id;
        $supplier_id = $request->supplier_id;
        $lead_time = $request->lead_time;
        $quantity = $request->quantity;
        $order_price = $request->order_price;
        $unit = $request->unit;
        $calc_price = $request->calc_price;
        $stock_storage_id = $request->stock_storage_id;
        $postage = $request->postage;

        $upload_file = $request->file('upload_file');

        try {

            if (!$stock_id) {
                // 在庫追加
                $stock = new Stock();
                $stock->name = $name;
                $stock->s_name = $s_name;
                $stock->jan_code = $jan_code;
                $stock->url = $url;
                $stock->img_path = $img_path ?? 'storage/stock/not-image-sample2.png';
                $stock->price = $price;
                $stock->purchase_identification_number = $purchase_identification_number;
                $stock->solo_unit = $solo_unit;
                $stock->org_unit = $org_unit;
                $stock->quantity_per_org = $quantity_per_org;
                $stock->classification_id = $classification_id;
                $stock->deli_location = $deli_location;
                $stock->stock_process_id = $base_stock_process_id;
                $stock->del_flg = 0;
                $stock->save();

                // 発注依頼データ作成
                $order_request = new OrderRequest();
                $order_request->stock_id = $stock->id;
                $order_request->request_user_id = $order_user;
                $order_request->user_id = $user_id;
                $order_request->supplier_id = $supplier_id;
                $order_request->lead_time = $lead_time;
                $order_request->quantity = $quantity;
                $order_request->price = $order_price;
                $order_request->unit = $unit;
                $order_request->calc_price = $calc_price;
                $order_request->new_stock_flg = 1;
                $order_request->postage = $postage;
                $order_request->stock_process_id = $order_stock_process_id;
                $order_request->save();

                // 稟議書がある場合
                if ($upload_file) {
                    // VPSのLaravel APIのエンドポイントURL（例）
                    $vpsApiUrl = 'https://akioka.cloud/api/order_request/upload_file';

                    // POSTリクエストをマルチパート形式で送信
                    $response = Http::asMultipart()->attach(
                        'upload_file', // 相手側のAPIが期待するinput名
                        file_get_contents($upload_file->getRealPath()),
                        $upload_file->getClientOriginalName()
                    )->post($vpsApiUrl, [
                        // 他に送信したいデータがあればここに追加
                        'order_request_id' => $order_request->id,
                    ]);
                }

                $stock_supplier = new StockSupplier();
                $stock_supplier->stock_id = $stock->id;
                $stock_supplier->supplier_id = $supplier_id;
                $stock_supplier->lead_time = $lead_time;
                $stock_supplier->postage = $postage ?? 0;
                $stock_supplier->save();
            } else {

                // 発注依頼データを作成
                $order_request = new OrderRequest();
                $order_request->stock_id = $stock_id;
                $order_request->request_user_id = $order_user;
                $order_request->user_id = $user_id;
                $order_request->supplier_id = $supplier_id;
                $order_request->lead_time = $lead_time;
                $order_request->quantity = $quantity;
                $order_request->price = $order_price;
                $order_request->unit = $unit;
                $order_request->calc_price = $calc_price;
                $order_request->new_stock_flg = 0;
                $order_request->postage = $postage;
                $order_request->stock_process_id = $order_stock_process_id;
                $order_request->save();
            }

            // 格納先が設定されている場合、発注点を更新
            if ($stock_storage_id) {
                // 発注点を自動更新
                $stock = StockStorage::select(
                    'stocks.*',
                    'stock_storages.reorder_point',
                    'stock_storages.quantity'
                )
                    ->join('stocks', 'stocks.id', 'stock_storages.stock_id')
                    ->where('stock_storages.id', $stock_storage_id)->first();

                // 発注依頼を記録
                $inventoryOperationRecord = new InventoryOperationRecord();
                $inventoryOperationRecord->inventory_operation_id = 7;
                $inventoryOperationRecord->stock_id = $stock_id;
                $inventoryOperationRecord->stock_storage_id = $stock_storage_id;
                $inventoryOperationRecord->bef_quantity = $stock->quantity;
                $inventoryOperationRecord->save();

                // 発注点再計算
                $reorder_point_avg = InventoryOperationRecord::where('stock_storage_id', $stock_storage_id)
                    ->where('inventory_operation_id', 7)
                    ->avg('bef_quantity');

                // 発注点を更新
                $stock_storage = StockStorage::find($stock_storage_id);
                $stock_storage->reorder_point = $reorder_point_avg;
                $stock_storage->save();
            }
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'message' => $msg]);
    }

    public function delete(Request $request)
    {
        $status = true;
        $msg = '';

        $initial_order_id = $request->initial_order_id;
        $order_request_id = null;



        try {


            $initial_order = InitialOrder::find($initial_order_id);
            $initial_order->del_flg = 1;
            $order_request_id = $initial_order->order_request_id;
            $initial_order->save();

            $order_request = OrderRequest::find($order_request_id);
            $order_request->status = 0;
            $order_request->save();

        } catch (Exception $e) {
            $status = false;
        }
    }

    public function update_date(Request $request)
    {
        $flg = $request->flg;
        $initial_order_id = $request->initial_order_id;
        $date = $request->date;

        $status = true;

        try {
            $initial_order = InitialOrder::find($initial_order_id);
            switch ($flg) {
                case 'desired':
                    // 納入希望日
                    $initial_order->desire_delivery_date = $date;
                    break;
                case 'expected':
                    // 納入予定日
                    $initial_order->expected_delivery_date = $date;
                    break;
                case 'delivery':
                    // 納入日
                    $initial_order->delivery_date = $date;
                    break;
            }
            $initial_order->save();
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }

    public function update_price(Request $request)
    {
        $status = true;
        $msg =  '';

        $initial_order_id = $request->initial_order_id;
        $price = $request->price;

        // return response()->json(['price' => $price]);

        try {
            $initial_order = InitialOrder::find($initial_order_id);
            if ($initial_order) {
                $order_request = OrderRequest::find($initial_order->order_request_id);
                if ($order_request) {
                    $order_request->price = $price;
                    $order_request->calc_price = $price * $order_request->quantity;
                    $order_request->status = 0;
                    $order_request->accept_flg = 0;
                    $order_request->save();

                    // 発注データを削除
                    $initial_order->delete();

                    // 再発注依頼を通知
                    $inventory_operation_record = new InventoryOperationRecord();
                    $inventory_operation_record->inventory_operation_id = 17;
                    $inventory_operation_record->stock_id = $order_request->stock_id;
                    $inventory_operation_record->save();
                } else {
                    $status = false;
                    $msg = '発注依頼データが見つかりません';
                }
            } else {
                $status = false;
                $msg = '発注データが見つかりません';
            }
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'msg' => $msg]);
    }

    public function update_data(Request $request)
    {
        $initial_order_id = $request->initial_order_id;
        $flg = $request->flg;
        $val = $request->val;

        $status = true;

        $initial_order = InitialOrder::find($initial_order_id);
        if ($initial_order) {
            switch ($flg) {
                case 'price':
                    $initial_order->price = $val;
                    $initial_order->calc_price = $val * $initial_order->quantity;

                    // マスタの単価も変更
                    $stock = Stock::find($initial_order->stock_id);
                    $stock->price = $val;
                    $stock->save();
                    break;
                case 'postage':
                    $initial_order->postage = $val;
                    break;
            }
            $initial_order->save();
        }

        return response()->json(['status' => $status]);
    }

    public function update_deli_file(Request $request)
    {
        $status = false;
        $msg = '';
        $response = null;

        try {
            if (!$request->hasFile('file')) {
                throw new Exception('ファイルがアップロードされていません。');
            }

            $file = $request->file('file');
            $initial_order_id = $request->initial_order_id;

            if (!$initial_order_id) {
                throw new Exception('発注IDが指定されていません。');
            }

            // ファイルの検証
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $extension = $file->getClientOriginalExtension();
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                throw new Exception('許可されていないファイル形式です。');
            }

            // ファイルサイズの制限（5MB）
            if ($file->getSize() > 5 * 1024 * 1024) {
                throw new Exception('ファイルサイズが大きすぎます。5MB以下にしてください。');
            }

            $order = InitialOrder::find($initial_order_id);
            if (!$order) {
                throw new Exception('指定された発注が見つかりません。');
            }

            // VPSのLaravel APIのエンドポイントURL
            $vpsApiUrl = 'https://akioka.cloud/api/file_upload/deli_file';

            // POSTリクエストをマルチパート形式で送信
            $response = Http::asMultipart()->attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post($vpsApiUrl, [
                'initial_order_id' => $initial_order_id,
            ]);

            $status = $response->successful();
            $msg = $response->body();
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        return response()->json([
            'status' => $status,
            'msg' => $msg,
            'details' => [
                'open' => true
            ]
        ]);
    }


    public function updateOrderComplete(Request $request)
    {
        $status = true;
        $initial_order_id = $request->initial_order_id;
        $order_complete_flg = $request->order_complete_flg;


        try {
            $initial_order = InitialOrder::select('initial_orders.*', 'order_requests.device_id as to_device_id')
                ->join('order_requests', 'order_requests.id', '=', 'initial_orders.order_request_id')
                ->where('initial_orders.id', $initial_order_id)
                ->first();

            $initial_order->order_complete_flg = $order_complete_flg;
            $initial_order->save();

            if ($order_complete_flg) {
                if ($initial_order->to_device_id && $initial_order->order_user_id != $initial_order->user_id) {
                    $msg = "以下の物品が発注されました。\n依頼者: $initial_order->order_user\n品名: $initial_order->name\n品番: $initial_order->s_name\n\n納品完了までもうしばらくお待ちください。";

                    Helper::createDeviceMessage(
                        0,
                        $initial_order->to_device_id, //依頼元端末
                        null,
                        $initial_order->order_user_id, //発注依頼者
                        $initial_order->user_id, //発注担当者
                        $msg
                    );
                }

                $user = User::find($initial_order->order_user_id);
                if ($user->email) { //メールアドレスが登録されている場合、メールを送信
                    $title = '発注が完了しました。';
                    $message = "{$initial_order->name} {$initial_order->s_name}の発注依頼が完了しました。";

                    Helper::createNotifyQueue($title, $message, '', [$user->id]);
                }
            }
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }

    // デバイスメッセージを取得
    public function sendDeviceMessage(Request $request)
    {
        $status = true;
        $msg = '';

        $device_notify_flg = $request->device_notify_flg;
        $initial_order_id = $request->initial_order_id;
        $message = $request->message;


        try {

            $initial_order = InitialOrder::select('initial_orders.*', 'order_requests.device_id as to_device_id')
                ->join('order_requests', 'order_requests.id', '=', 'initial_orders.order_request_id')
                ->where('initial_orders.id', $initial_order_id)
                ->first();
            $initial_order->description = $message;

            if ($device_notify_flg) {
                $device_message_id = Helper::createDeviceMessage(
                    1,
                    $initial_order->to_device_id,
                    null,
                    $initial_order->order_user_id, //発注依頼者
                    $initial_order->user_id, //発注担当者
                    $initial_order->description
                );
                $initial_order->device_message_id = $device_message_id;
            }
            $initial_order->save();
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'msg' => $msg]);
    }
}
