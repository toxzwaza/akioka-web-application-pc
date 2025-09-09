<?php

namespace App\Http\Controllers;

use App\Http\Services\Helper;
use App\Models\Document;
use App\Models\DocumentImage;
use App\Models\InitialOrder;
use App\Models\OrderRequest;
use App\Models\OrderRequestApproval;
use App\Models\Stock;
use App\Models\StockSupplier;
use App\Models\Supplier;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class OrderRequestController extends Controller
{
    //
    //
    // 発注依頼一覧
    public function index(Request $request)
    {
        $user_id = $request->user_id ?? null;

        // 発注可能ユーザー（総務部）
        $order_users = User::select('users.id', 'users.name')->where('is_admin', 1)->get();

        return Inertia::render('Stock/OrderRequests', ['order_users' => $order_users, 'user_id' => $user_id]);
    }

    public function getOrderRequests(Request $request)
    {
        $status = true;
        $msg = "";

        $user_id = $request->user_id ?? null;

        try {
            // 未受理の注文依頼のみ取得
            // 最大のquantityを取得
            $order_requests = OrderRequest::select(
                'order_requests.id',
                'order_requests.stock_id',
                'order_requests.id as order_request_id',
                'order_requests.accept_flg',
                'stocks.img_path',
                'stocks.name',
                'stocks.s_name',
                'order_requests.name as order_request_name',
                'order_requests.s_name as order_request_s_name',
                'stocks.url',
                'order_requests.now_quantity', //現在個数
                'order_requests.quantity',
                'order_requests.price',
                'order_requests.unit',
                'order_requests.created_at', //依頼日
                'order_requests.digest_date', //消化予定日
                'order_requests.desire_delivery_date', //希望納期
                'order_requests.file_path',
                'order_requests.description',
                'order_requests.sub_description',
                'order_requests.document_id',  //稟議書
                'users.name as request_user_name',
                'users.id as request_user_id',
                'order_users.name as order_user_name',
                'order_users.id as order_user_id',
                'order_requests.postage',
                'order_requests.calc_price',
                'order_requests.new_stock_flg',
                'suppliers.name as supplier_name',
                'order_requests.supplier_id',
                'stock_suppliers.lead_time as stock_supplier_lead_time',
                'max_stock_storages.max_quantity as stock_storage_quantity',
                'max_stock_storages.reorder_point as reorder_point',
                'device_messages.message as message',
                'device_messages.answer as answer',
                'device_messages.read_flg',
            )
                ->leftJoin('stocks', 'stocks.id', '=', 'order_requests.stock_id')
                ->leftJoin('users', 'users.id', '=', 'order_requests.request_user_id')
                ->leftJoin('users as order_users', 'order_users.id', '=', 'order_requests.user_id')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'order_requests.supplier_id')
                ->leftJoin('stock_suppliers', function ($join) {
                    $join->on('stock_suppliers.stock_id', '=', 'stocks.id')
                        ->on('stock_suppliers.supplier_id', '=', 'suppliers.id');
                })
                ->leftJoin(DB::raw('(SELECT stock_id, MAX(quantity) as max_quantity, MAX(reorder_point) as reorder_point FROM stock_storages GROUP BY stock_id) as max_stock_storages'), 'max_stock_storages.stock_id', '=', 'stocks.id')
                ->leftJoin('device_messages', 'device_messages.id', '=', 'order_requests.device_message_id')
                ->leftJoin('document_images', 'document_images.document_id', 'order_requests.document_id')
                ->where('order_requests.del_flg', '=', 0)
                ->where('order_requests.status', '=', 0)
                ->where(function ($query) use ($user_id) {
                    $query->where('order_requests.user_id', '=', $user_id)
                        ->orWhereNull('order_requests.user_id');
                })
                ->orderBy('order_requests.user_id', 'desc')
                ->orderBy('order_requests.desire_delivery_date', 'asc')
                ->orderBy('order_requests.created_at', 'desc')
                ->distinct()
                ->get();


            foreach ($order_requests as $order_request) {
                if (!$order_request->supplier_id) { //取引先を初期化
                    $stock_supplier = StockSupplier::select('suppliers.id', 'suppliers.name', 'stock_suppliers.lead_time')->where('stock_id', $order_request->stock_id)
                        ->join('suppliers', 'suppliers.id', 'stock_suppliers.supplier_id')->first();


                    if ($stock_supplier) {
                        $order_request->supplier_id = $stock_supplier->id;
                        $order_request->save();
                        $order_request->supplier_name = $stock_supplier->name;
                    }
                }

                // 承認状況を取得
                $order_request_approvals = OrderRequestApproval::select('users.id as user_id', 'users.name', 'ora.status', 'ora.final_flg', 'ora.comment', 'ora.updated_at')
                    ->where('order_request_id', $order_request->id)
                    ->join('users', 'users.id', '=', 'ora.user_id')
                    ->from('order_request_approvals as ora')
                    ->get();
                $order_request->order_request_approvals = $order_request_approvals;

                if($order_request->document_id){
                    $document = Document::find($order_request->document_id);
                    if ($document) {
                        $order_request->document_data = $document;
                        $document_images = DocumentImage::select('image_path')
                            ->where('document_id', $order_request->document_id)
                            ->where('extension', '!=', 'pdf')
                            ->get();
                        $order_request->document_data->document_images = $document_images;
                    }
                }


            }
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }



        return response()->json(['status' => $status, 'msg' => $msg, 'order_requests' => $order_requests]);
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
                $initial_order->order_request_id = $order_request->id;
                $initial_order->stock_id = $order_request->stock_id;
                $initial_order->order_no = Helper::createOrderNo();
                $initial_order->order_date = date('Y-m-d');
                $initial_order->com_no = $supplier->supplier_no ?? '';
                $initial_order->com_name = $supplier->name;
                $initial_order->name = $stock->name;
                $initial_order->s_name = $stock->s_name;
                $initial_order->order_unit = $order_request->unit ?? $stock->solo_unit;
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
                $initial_order->stock_process_id = $order_request->stock_process_id;
                $initial_order->file_path = $order_request->file_path;
                $initial_order->desire_delivery_date = $order_request->desire_delivery_date;
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

    public function updateOrderRequest(Request $request)
    {
        $status = true;

        $order_request_id = $request->order_request_id;
        $quantity = $request->quantity;
        $price = $request->price;
        $calc_price = $request->calc_price;
        $postage = $request->postage;
        $is_update_price = $request->is_update_price;
        $is_update_postage = $request->is_update_postage;
        $supplier_id = $request->supplier_id;


        try {
            $order_request = OrderRequest::find($order_request_id);
            $order_request->quantity = $quantity;
            $order_request->price = $price;
            $order_request->calc_price = $calc_price;
            $order_request->postage = $postage;
            $order_request->save();

            // マスタに設定する場合は追記
            if ($is_update_price) {
                $stock = Stock::find($order_request->stock_id);
                $stock->price = $price;
                $stock->save();
            }

            if ($is_update_postage && $supplier_id) {
                $stock_supplier = StockSupplier::where('stock_id', $order_request->stock_id)->where('supplier_id', $supplier_id)->first();
                if ($stock_supplier) {
                    $stock_supplier->postage = $postage;
                    $stock_supplier->save();
                }
            }
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }

    public function updateSubDescription(Request $request)
    {
        $status = true;
        $order_request_id = $request->order_request_id;
        $sub_description = $request->sub_description;

        try {
            $order_request = OrderRequest::find($order_request_id);
            $order_request->sub_description = $sub_description;
            $order_request->save();
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }

    public function savePDF(Request $request)
    {
        try {
            $imageData = $request->input('pdfData');
            $filename = $request->input('filename');
            $orders = $request->input('orders');

            // Base64データから画像データを取得
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));

            // public/purchaseディレクトリに保存
            Storage::disk('public')->put('purchase/' . $filename, $imageData);

            // 発注書を発注データに紐づけ
            foreach ($orders as $order) {
                $initial_order = InitialOrder::where('id', $order)->first();
                if ($initial_order) {
                    $initial_order->purchase_path = 'purchase/' . $filename;
                    $initial_order->save();
                }
            }



            return response()->json([
                'status' => true,
                'message' => 'Image saved successfully',
                'path' => 'purchase/' . $filename
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 発注依頼一覧から
    public function storeApprovalDocument(Request $request)
    {
        $status = true;

        $upload_file = $request->file('upload_file');
        $order_request_id = $request->order_request_id;

        try {
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
                    'order_request_id' => $order_request_id,
                ]);
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

        $order_request_id = $request->order_request_id;
        $message = $request->message;
        $user_id = $request->user_id;


        try {

            $order_request = OrderRequest::find($order_request_id);
            $device_message_id = Helper::createDeviceMessage(
                2,
                $order_request->device_id,
                null,
                $order_request->request_user_id,
                $order_request->user_id ?? $user_id,
                $message
            );

            $order_request->device_message_id = $device_message_id;
            $order_request->save();
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'msg' => $msg]);
    }

    public function reloadSupplier(Request $request){
        $status = true;
        $msg = "";

        $order_request_id = $request->order_request_id;

        try{
            $order_request = OrderRequest::find($order_request_id);
            $stock_supplier = StockSupplier::where('stock_id', $order_request->stock_id)->where('main_flg', 1)->first();

            if($stock_supplier){
                $order_request->supplier_id = $stock_supplier->supplier_id;
                $order_request->save();
            }
        }catch(Exception $e){
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'msg' => $msg]);
    }

    public function updateStockId(Request $request){
        $status = true;
        $msg = "";
        $order_request_id = $request->order_request_id;
        $stock_id = $request->stock_id;

        $order_request = OrderRequest::find($order_request_id);
        $stock = Stock::find($stock_id);

        try{
            $order_request->stock_id = $stock_id;
            $order_request->save();
            $msg = "品名: " . $stock->name . " 品番: " . $stock->s_name . " で登録を行いました。";

            // main_flgが立っているものを優先、なければ最初のものを取得
            $stock_supplier = StockSupplier::where('stock_id', $stock_id)
                ->orderByRaw('main_flg DESC')
                ->first();

            if($stock_supplier){
                $order_request->supplier_id = $stock_supplier->supplier_id;
                $order_request->supplier_name = $stock_supplier->supplier->name;
                $order_request->stock_supplier_lead_time = $stock_supplier->lead_time;
                $order_request->price = $stock->price;
                $order_request->calc_price = $stock->price * $order_request->quantity;
                $order_request->postage = $stock_supplier->postage;
                $order_request->lead_time = $stock_supplier->lead_time;
                
                $order_request->save();
            }
        }catch(Exception $e){
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'msg' => $msg]);
    }
}
