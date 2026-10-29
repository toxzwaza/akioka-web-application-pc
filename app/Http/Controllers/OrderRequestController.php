<?php

namespace App\Http\Controllers;

use App\Http\Services\Helper;
use App\Models\Device;
use App\Models\Document;
use App\Models\DocumentImage;
use App\Models\InitialOrder;
use App\Models\OrderRequest;
use App\Models\OrderRequestApproval;
use App\Models\Stock;
use App\Models\StockProcess;
use App\Models\StockSupplier;
use App\Models\Supplier;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // デバイス一覧
        $devices = Device::select('devices.id', 'devices.name')->get();

        // 工程一覧
        $stock_processes = StockProcess::all();

        return Inertia::render('Stock/OrderRequests', ['order_users' => $order_users, 'user_id' => $user_id, 'devices' => $devices, 'stock_processes' => $stock_processes, 'file_base_url' => config('services.akioka_cloud.url')]);
    }

    public function getOrderRequests(Request $request)
    {
        $status = true;
        $msg = "";
        $order_requests = [];

        $user_id = $request->user_id ?? null;

        try {
            // 未受理の注文依頼のみ取得
            // 最大のquantityを取得
            $order_requests = OrderRequest::select(
                'order_requests.id',
                'order_requests.stock_id',
                'order_requests.id as order_request_id',
                'order_requests.user_id', //並び替え用（DISTINCT + ORDER BY のため SELECT に含める）
                'order_requests.accept_flg',
                'stocks.img_path',
                'stocks.name',
                'stocks.s_name',
                'stocks.desc_memo as stock_desc_memo',
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
                'order_requests.lead_time', //リードタイム
                'order_requests.file_path',
                'order_requests.file_path_sub',
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
                'devices.id as device_id',
                'devices.name as device_name',
                DB::raw('COALESCE(order_requests.stock_process_id, stocks.stock_process_id) as stock_process_id'),
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
                ->leftJoin('devices', 'devices.id', 'order_requests.device_id')
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


            // 承認情報を一括取得（N+1回避）
            $orderRequestIds = $order_requests->pluck('id')->toArray();
            $allApprovals = OrderRequestApproval::select('users.id as user_id', 'users.name', 'ora.status', 'ora.final_flg', 'ora.comment', 'ora.updated_at', 'ora.order_request_id')
                ->whereIn('order_request_id', $orderRequestIds)
                ->join('users', 'users.id', '=', 'ora.user_id')
                ->from('order_request_approvals as ora')
                ->get()
                ->groupBy('order_request_id');

            // 稟議書情報を一括取得（N+1回避）
            $documentIds = $order_requests->pluck('document_id')->filter()->unique()->toArray();
            $allDocuments = !empty($documentIds) ? Document::whereIn('id', $documentIds)->get()->keyBy('id') : collect();
            $allDocumentImages = !empty($documentIds) ? DocumentImage::select('document_id', 'image_path')
                ->whereIn('document_id', $documentIds)
                ->where('extension', '!=', 'pdf')
                ->get()
                ->groupBy('document_id') : collect();

            // 取引先未設定の在庫IDを一括取得（N+1回避）
            $stockIdsWithoutSupplier = $order_requests->where('supplier_id', null)->pluck('stock_id')->unique()->toArray();
            $stockSuppliers = !empty($stockIdsWithoutSupplier) ? StockSupplier::select('suppliers.id', 'suppliers.name', 'stock_suppliers.lead_time', 'stock_suppliers.stock_id')
                ->whereIn('stock_suppliers.stock_id', $stockIdsWithoutSupplier)
                ->join('suppliers', 'suppliers.id', 'stock_suppliers.supplier_id')
                ->get()
                ->keyBy('stock_id') : collect();

            foreach ($order_requests as $order_request) {
                if (!$order_request->supplier_id) {
                    $stock_supplier = $stockSuppliers->get($order_request->stock_id);
                    if ($stock_supplier) {
                        $order_request->supplier_id = $stock_supplier->id;
                        $order_request->save();
                        $order_request->supplier_name = $stock_supplier->name;
                    }
                }

                $order_request->order_request_approvals = $allApprovals->get($order_request->id, collect());

                if ($order_request->document_id) {
                    $document = $allDocuments->get($order_request->document_id);
                    if ($document) {
                        $order_request->document_data = $document;
                        $order_request->document_data->document_images = $allDocumentImages->get($order_request->document_id, collect());
                    }
                }

                // file_path_subを配列に変換
                if ($order_request->file_path_sub) {
                    if (is_string($order_request->file_path_sub)) {
                        $decoded = json_decode($order_request->file_path_sub, true);
                        $order_request->file_path_sub = $decoded !== null ? $decoded : [];
                    } elseif (!is_array($order_request->file_path_sub)) {
                        $order_request->file_path_sub = [];
                    }
                } else {
                    $order_request->file_path_sub = [];
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
                // 同じ発注依頼から過去に作成された発注があれば、最初に振られた注文Noを引き継ぐ
                // （単価値上がり等で再承認 → 再発注した場合に注文Noを維持する）
                $previous_order = InitialOrder::where('order_request_id', $order_request->id)
                    ->whereNotNull('order_no')
                    ->orderBy('id', 'asc')
                    ->first();
                $initial_order->order_no = $previous_order ? $previous_order->order_no : Helper::createOrderNo();
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
        $solo_unit = $request->solo_unit;
        $is_update_price = $request->is_update_price;
        $is_update_postage = $request->is_update_postage;
        $is_update_solo_unit = $request->is_update_solo_unit;
        $supplier_id = $request->supplier_id;
        $desire_delivery_date = $request->desire_delivery_date;
        $stock_process_id = $request->stock_process_id;
        $is_update_stock_process_id = $request->is_update_stock_process_id;


        try {
            $order_request = OrderRequest::find($order_request_id);

            // 希望納期の更新
            if ($desire_delivery_date) {
                // 変更前の希望納期を保存
                $old_desire_delivery_date = $order_request->desire_delivery_date;

                // 希望納期が変更された場合、依頼元端末にメッセージを送信
                if ($old_desire_delivery_date != $desire_delivery_date && $order_request->device_id) {
                    try {
                        $stock_name = $order_request->stock_id
                            ? (Stock::find($order_request->stock_id) ? Stock::find($order_request->stock_id)->name : $order_request->order_request_name)
                            : $order_request->order_request_name;

                        $old_date = $old_desire_delivery_date ? date('Y年m月d日', strtotime($old_desire_delivery_date)) : '未設定';
                        $new_date = date('Y年m月d日', strtotime($desire_delivery_date));

                        $message = "【希望納期変更通知】\n";
                        $message .= "品名: {$stock_name}\n";
                        $message .= "変更前: {$old_date}\n";
                        $message .= "変更後: {$new_date}";

                        Helper::createDeviceMessage(
                            1, // priority
                            $order_request->device_id, // to_device_id
                            null, // from_device_id
                            $order_request->request_user_id, // to_user_id
                            $order_request->user_id, // from_user_id
                            $message
                        );

                    } catch (Exception $e) {
                        Log::error('Device message error: ' . $e->getMessage());
                        // メッセージ送信エラーでも処理は継続
                    }
                }

                $order_request->desire_delivery_date = $desire_delivery_date;
            }

            // 数量、単価、金額、送料の更新
            if ($quantity !== null) {
                $order_request->quantity = $quantity;
            }
            if ($price !== null) {
                $order_request->price = $price;
            }
            if ($calc_price !== null) {
                $order_request->calc_price = $calc_price;
            }
            if ($postage !== null) {
                $order_request->postage = $postage;
            }
            if ($stock_process_id !== null) {
                $order_request->stock_process_id = $stock_process_id;
            }
            if($solo_unit !== null){
                $order_request->unit = $solo_unit;
            }

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

            if ($is_update_stock_process_id) {
                $stock = Stock::find($order_request->stock_id);
                $stock->stock_process_id = $stock_process_id;
                $stock->save();
            }
            if($is_update_solo_unit){
                $stock = Stock::find($order_request->stock_id);
                $stock->solo_unit = $solo_unit;
                $stock->save();
            }


        } catch (Exception $e) {
            $status = false;
            Log::error('OrderRequest update error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
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
                    $initial_order->purchase_path = config('app.url') . '/storage/purchase/' . $filename;
                    $initial_order->save();
                }
            }



            return response()->json([
                'status' => true,
                'message' => 'Image saved successfully',
                'path' => config('app.url') . '/storage/purchase/' . $filename
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 発注依頼一覧から
    // mode=append：既存の稟議書の後ろに追加／mode=replace：アップロード済みの稟議書をすべて新しいものに入れ替える
    // （document_id のシステム内稟議書は対象外）
    public function storeApprovalDocument(Request $request)
    {
        $status = true;
        $message = '';
        $file_urls = [];

        $upload_files = $request->file('upload_files') ?? [];
        if ($request->file('upload_file')) {
            $upload_files[] = $request->file('upload_file');
        }
        $order_request_id = $request->order_request_id;
        $mode = $request->mode === 'replace' ? 'replace' : 'append';
        $before_paths = null;
        $saved = false;

        try {
            if (count($upload_files) === 0) {
                throw new Exception('ファイルがアップロードされていません。');
            }

            $order_request = OrderRequest::find($order_request_id);
            if (!$order_request) {
                throw new Exception('発注依頼が見つかりません。');
            }

            // 物品システムのAPIは保存のたびに file_path を上書きするため、呼ぶ前の添付を控えておく
            $before_paths = $this->approvalFilePaths($order_request);

            // ファイル名が秒単位で決まるため、1件ずつ順番に保存する
            foreach ($upload_files as $upload_file) {
                $response = Http::withoutVerifying()
                    ->asMultipart()
                    ->attach(
                        'upload_file',
                        file_get_contents($upload_file->getRealPath()),
                        $upload_file->getClientOriginalName()
                    )
                    ->post($this->akiokaCloudUrl('/api/order_request/upload_file'), [
                        'order_request_id' => $order_request_id,
                    ]);

                if (!$response->successful() || !$response->json('status') || !$response->json('file_url')) {
                    throw new Exception('物品システムへのファイル保存に失敗しました。' . ($response->json('message') ?? ''));
                }
                $file_urls[] = $response->json('file_url');
            }

            DB::transaction(function () use ($order_request_id, $mode, $before_paths, $file_urls) {
                $order_request = OrderRequest::lockForUpdate()->find($order_request_id);
                $paths = $mode === 'replace' ? $file_urls : array_merge($before_paths, $file_urls);
                $this->saveApprovalFilePaths($order_request, $paths);
            });
            $saved = true;

            // 入れ替えで外れたファイルは、他の発注依頼が使っていなければ削除する
            if ($mode === 'replace') {
                foreach (array_diff($before_paths, $file_urls) as $removed_path) {
                    $this->deleteApprovalFileIfUnused($removed_path);
                }
            }

            $message = ($mode === 'replace' ? '稟議書を入れ替えました。' : '稟議書を追加しました。');
        } catch (Exception $e) {
            $status = false;
            $message = $e->getMessage();
            Log::error('storeApprovalDocument error: ' . $e->getMessage());

            // 途中で失敗したときは、物品システムが上書きした file_path を元に戻し、保存済みのファイルを片付ける
            if ($before_paths !== null && !$saved && count($file_urls) > 0) {
                $order_request = OrderRequest::find($order_request_id);
                $this->saveApprovalFilePaths($order_request, $before_paths);
                foreach ($file_urls as $file_url) {
                    $this->deleteApprovalFileIfUnused($file_url);
                }
                $file_urls = [];
            }
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'file_url' => $file_urls[0] ?? null,
            'file_urls' => $file_urls,
        ]);
    }

    // ファイル削除（file_path の稟議書も削除できる。残りの先頭を file_path にする）
    public function deleteApprovalDocument(Request $request)
    {
        $status = true;
        $message = '';

        $order_request_id = $request->order_request_id;
        $file_path = $request->file_path;

        try {
            if (!$file_path) {
                throw new Exception('ファイルパスが指定されていません。');
            }

            DB::transaction(function () use ($order_request_id, $file_path) {
                $order_request = OrderRequest::lockForUpdate()->find($order_request_id);
                if (!$order_request) {
                    throw new Exception('発注依頼が見つかりません。');
                }

                $paths = $this->approvalFilePaths($order_request);
                if (!in_array($file_path, $paths, true)) {
                    throw new Exception('指定されたファイルが見つかりません。');
                }

                $this->saveApprovalFilePaths($order_request, array_diff($paths, [$file_path]));
            });

            $message = $this->deleteApprovalFileIfUnused($file_path)
                ? 'ファイル削除成功'
                : '発注依頼から外しました（ファイル本体は他の発注依頼で使用中、または削除に失敗したため残しています）。';
        } catch (Exception $e) {
            $status = false;
            $message = $e->getMessage();
            Log::error('deleteApprovalDocument error: ' . $e->getMessage());
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
        ]);
    }

    // 発注依頼に添付された稟議書ファイルの一覧（file_path を先頭に、重複を除く）
    private function approvalFilePaths(OrderRequest $order_request): array
    {
        $sub = $order_request->file_path_sub;
        $paths = array_merge(
            $order_request->file_path ? [$order_request->file_path] : [],
            is_array($sub) ? array_filter($sub, 'is_string') : []
        );

        return array_values(array_unique(array_filter($paths)));
    }

    // 先頭を file_path、残りを file_path_sub に保存する（物品システム側の画面は file_path だけを見るため）
    private function saveApprovalFilePaths(OrderRequest $order_request, array $paths): void
    {
        $paths = array_values(array_unique(array_filter($paths)));
        $order_request->file_path = $paths[0] ?? null;
        $order_request->file_path_sub = array_slice($paths, 1) ?: null;
        $order_request->save();
    }

    // 他の発注依頼が参照していないときだけ、物品システム上のファイル本体を削除する
    private function deleteApprovalFileIfUnused(string $file_path): bool
    {
        $in_use = OrderRequest::where('file_path', $file_path)
            ->orWhere('file_path_sub', 'like', '%' . str_replace('/', '\\\\/', $file_path) . '%')
            ->orWhere('file_path_sub', 'like', '%' . $file_path . '%')
            ->exists();
        if ($in_use) {
            return false;
        }

        try {
            $response = Http::withoutVerifying()->delete($this->akiokaCloudUrl('/api/order_request/delete_file'), [
                'file_path' => $file_path,
            ]);
            if ($response->successful() && $response->json('status')) {
                return true;
            }
            Log::warning('VPS API delete_file failed: ' . $response->body());
        } catch (Exception $e) {
            Log::warning('VPS API delete_file error: ' . $e->getMessage());
        }

        return false;
    }

    private function akiokaCloudUrl(string $path): string
    {
        return config('services.akioka_cloud.url') . $path;
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

    public function reloadSupplier(Request $request)
    {
        $status = true;
        $msg = "";

        $order_request_id = $request->order_request_id;

        try {
            $order_request = OrderRequest::find($order_request_id);
            $stock_supplier = StockSupplier::where('stock_id', $order_request->stock_id)->where('main_flg', 1)->first();

            if ($stock_supplier) {
                $order_request->supplier_id = $stock_supplier->supplier_id;
                $order_request->save();
            }
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'msg' => $msg]);
    }

    public function updateStockId(Request $request)
    {
        $status = true;
        $msg = "";
        $order_request_id = $request->order_request_id;
        $stock_id = $request->stock_id;

        $order_request = OrderRequest::find($order_request_id);
        $stock = Stock::find($stock_id);

        try {
            $order_request->stock_id = $stock_id;
            $order_request->save();
            $msg = "品名: " . $stock->name . " 品番: " . $stock->s_name . " で登録を行いました。";

            // main_flgが立っているものを優先、なければ最初のものを取得
            $stock_supplier = StockSupplier::where('stock_id', $stock_id)
                ->orderByRaw('main_flg DESC')
                ->first();

            if ($stock_supplier) {
                $order_request->supplier_id = $stock_supplier->supplier_id;
                $order_request->supplier_name = $stock_supplier->supplier->name;
                $order_request->stock_supplier_lead_time = $stock_supplier->lead_time;
                $order_request->price = $stock->price;
                $order_request->calc_price = $stock->price * $order_request->quantity;
                $order_request->postage = $stock_supplier->postage;
                $order_request->lead_time = $stock_supplier->lead_time;

                $order_request->save();
            }
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'msg' => $msg]);
    }

    /**
     * 発注依頼にデバイスIDを設定
     */
    public function setDeviceId(Request $request)
    {
        $status = true;
        $msg = "";

        try {
            // バリデーション
            $request->validate([
                'device_id' => 'required|exists:devices,id',
                'order_request_id' => 'required|exists:order_requests,id'
            ]);

            $device_id = $request->device_id;
            $order_request_id = $request->order_request_id;

            // 発注依頼を取得
            $order_request = OrderRequest::find($order_request_id);

            if (!$order_request) {
                throw new Exception('発注依頼が見つかりません。');
            }

            // デバイスを取得（デバイス名も一緒に保存したい場合）
            $device = Device::find($device_id);

            if (!$device) {
                throw new Exception('デバイスが見つかりません。');
            }

            // デバイスIDを設定
            $order_request->device_id = $device_id;
            $order_request->save();

            $msg = "デバイス「{$device->name}」を発注依頼に設定しました。";
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'msg' => $msg]);
    }
}
