<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\InitialOrder;
use App\Models\InventoryOperation;
use App\Models\InventoryOperationRecord;
use App\Models\LastTreatRecord;
use App\Models\Location;
use App\Models\Process;
use App\Models\RetainedStock;
use App\Models\Stock;
use App\Models\StockStorage;
use App\Models\StockSupplier;
use App\Models\StorageAddress;
use App\Models\Supplier;
use App\Models\User;
use App\Services\Method;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StockController extends Controller
{
    public function test()
    {
        // 出庫もしくは超過出庫したモノの一覧
        // $items = InventoryOperationRecord::select('stocks.id as stock_id', 'stocks.name as stock_name', 'inventory_operations.name as operation_name', 'inventory_operations.id as operation_id', 'inventory_operation_records.created_at', 'users.name as user_name', 'inventory_operation_records.quantity', 'inventory_operation_records.est_quantity')->join('inventory_operations', 'inventory_operations.id', 'inventory_operation_records.inventory_operation_id')->join('stock_storages', 'stock_storages.id', 'inventory_operation_records.stock_storage_id')->join('stocks', 'stocks.id', 'inventory_operation_records.stock_id')->join('users', 'users.id', 'inventory_operation_records.user_id')->orderby('inventory_operation_records.updated_at', 'desc')->distinct('stocks.id')->orderby('stocks.id', 'desc')->get();
        $items = [];
        for ($i = 7; $i <= now()->month; $i++) {
            $monthly_items = InventoryOperationRecord::select('stocks.id', 'stocks.name as stock_name', DB::raw('SUM(inventory_operation_records.quantity) as total_quantity'))
                ->join('stocks', 'stocks.id', 'inventory_operation_records.stock_id')
                ->whereYear('inventory_operation_records.created_at', 2024) // 2024年
                ->whereMonth('inventory_operation_records.created_at', $i)   // 各月
                ->groupBy('stocks.id', 'stocks.name') // 'stocks.name'を追加
                ->get();

            foreach ($monthly_items as $item) {
                $items[$item->id]['stock_name'] = $item->stock_name; // stock_nameを格納
                $items[$item->id]['monthly'][$i] = $item->total_quantity;
            }
        }

        // 平均と合計を計算して追加
        foreach ($items as $id => $data) {
            $total = array_sum($data['monthly']);
            $count = count($data['monthly']);
            $average = $total / 5;
            $items[$id]['average'] = $average;
            $items[$id]['total'] = $total; // 合計を追加
        }
        // dd($items);



        // foreach ($items as $item) {
        //     echo '<p>' . $item['stock_name'] . ' : 平均=' . $item['average'] . '/月 :合計=' . $item['total'] . '</p>';
        // }




        // CSV出力用のヘッダー
        $csvHeader = ['Stock ID', 'Stock Name', 'Average', 'Total', 'Monthly Quantities'];



        // CSVデータの準備
        $csvData = [];
        foreach ($items as $id => $data) {
            $monthlyQuantities = implode(', ', $data['monthly']);
            $csvData[] = [
                $id,
                $data['stock_name'],
                $data['average'],
                $data['total'],
                $monthlyQuantities
            ];
        }


        // CSVファイルの作成
        $fileName = 'stock_data_' . date('Ymd_His') . '.csv';
        $file = fopen($fileName, 'w');
        fputcsv($file, $csvHeader);

        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }

        fclose($file);

        // CSVファイルをダウンロード
        return response()->download($fileName)->deleteFileAfterSend(true);
    }
    //
    public function index()
    {
        $operation_records = InventoryOperationRecord::select('stocks.name as stock_name', 'inventory_operations.name as operation_name', 'inventory_operations.id as operation_id', 'inventory_operation_records.created_at', 'users.name as user_name', 'inventory_operation_records.quantity', 'inventory_operation_records.est_quantity')->join('inventory_operations', 'inventory_operations.id', 'inventory_operation_records.inventory_operation_id')->join('stock_storages', 'stock_storages.id', 'inventory_operation_records.stock_storage_id')->join('stocks', 'stocks.id', 'inventory_operation_records.stock_id')->join('users', 'users.id', 'inventory_operation_records.user_id')->orderby('inventory_operation_records.updated_at', 'desc')->paginate(25);
        // dd($operation_records);


        $operation_record_recent = [];
        for ($i = 0; $i < 6; $i++) {
            $recent_count = InventoryOperationRecord::whereDate('created_at', now()->subDays(5 - $i)->toDateString())
                ->count();
            $operation_record_recent[] = $recent_count;
        }
        // dd($operation_record_recent);

        // dd($operation_record_recent);


        return view('stock.index', compact('operation_records', 'operation_record_recent'));
    }
    // 在庫一覧
    public function stocks(Request $request)
    {
        $keyword = $request->keyword;
        $storage_address_id = $request->storage_address_id;

        if ($storage_address_id) {

            if ($keyword) {
                // 格納アドレスと検索キーワード
                $stocks = StockStorage::select('stocks.*')
                    ->join('stocks', 'stocks.id', 'stock_storages.stock_id')
                    ->where('storage_address_id', $storage_address_id)
                    ->where('stocks.del_flg', 0)
                    ->where(function ($query) use ($keyword) {
                        $query->where('stocks.name', 'like', "%$keyword%")
                            ->orWhere('stocks.s_name', 'like', "%$keyword%")
                            ->orWhere('stocks.jan_code', 'like', "%$keyword%");
                    })
                    ->orderby('updated_at', 'desc')
                    ->paginate(20);

                // $stocks = Stock::select('stocks.*', 'classifications.name as classification_name')->join('classifications', 'stocks.classification_id', 'classifications.id')->where('stocks.name', 'like', "%$keyword%")->orWhere('stocks.s_name', 'like', "%$keyword%")->orWhere('stocks.jan_code', 'like', "%$keyword%")->orderby('stocks.updated_at', 'desc')->paginate(20);
            } else {
                // 格納アドレスのみ

                $stocks = StockStorage::select('stocks.*')
                    ->join('stocks', 'stocks.id', 'stock_storages.stock_id')
                    ->where('storage_address_id', $storage_address_id)
                    ->where('stocks.del_flg', 0)
                    ->orderby('updated_at', 'desc')->paginate(20);
            }


            return view('stock.stocks', compact('stocks'));
        }


        if ($keyword) {
            // キーワード検索のみ
            $stocks = Stock::where('stocks.del_flg', 0)
                ->where('stocks.name', 'like', "%$keyword%")
                ->orWhere('stocks.s_name', 'like', "%$keyword%")
                ->orWhere('stocks.jan_code', 'like', "%$keyword%")
                ->orderby('stocks.updated_at', 'desc')->paginate(20);
        } else {
            // 検索条件なし
            $stocks = Stock::where('stocks.del_flg', 0)
                ->orderby('stocks.updated_at', 'desc')->paginate(20);
        }

        return view('stock.stocks', compact('stocks'));
    }

    // 
    public function storage_address(Request $request)
    {
        $location_id = $request->location_id;
        if ($location_id) {
            $storage_location_addresses = StorageAddress::select('storage_addresses.id as storage_address_id', 'locations.name as location_name', 'storage_addresses.address', 'storage_addresses.created_at')->join('locations', 'locations.id', 'storage_addresses.location_id')
                ->where('location_id', $location_id)
                ->orderby('address', 'asc')->paginate(20);
        } else {

            $storage_location_addresses = StorageAddress::select('storage_addresses.id as storage_address_id', 'locations.name as location_name', 'storage_addresses.address', 'storage_addresses.created_at')->join('locations', 'locations.id', 'storage_addresses.location_id')->orderby('address', 'asc')->paginate(20);
        }

        foreach ($storage_location_addresses as $sla) {
            $stock_storage_count = StockStorage::join('stocks', 'stocks.id', 'stock_storages.stock_id')
                ->join('classifications', 'stocks.classification_id', 'classifications.id')
                ->where('storage_address_id', $sla->storage_address_id)
                ->where('stocks.del_flg', 0)
                ->where('stocks.not_stock_flg', 0)
                ->count();


            $sla->count = $stock_storage_count;
        }




        return view('stock.stock_storages', compact('storage_location_addresses'));
    }
    public function suppliers()
    {


        $suppliers = Supplier::orderby('updated_at', 'desc')->paginate(20);

        return view('stock.suppliers', compact('suppliers'));
    }


    // 在庫編集
    public function stock_edit($stock_id)
    {
        $stock = Stock::where('id', $stock_id)->first();
        $classifications = Classification::all();
        $processes = Process::all();
        $locations = Location::all();

        $stock_suppliers = StockSupplier::select('stock_suppliers.id as stock_supplier_id', 'stock_suppliers.memo as stock_supplier_memo', 'suppliers.*', 'stock_suppliers.lead_time', 'stock_suppliers.act_flg')->where('stock_id', $stock_id)->join('suppliers', 'suppliers.id', 'stock_suppliers.supplier_id')->get();



        // 在庫状況
        $stock_storages = StockStorage::select('stock_storages.id as stock_storage_id', 'quantity', 'locations.name as location_name', 'address', 'location_id', 'storage_addresses.id as storage_address_id')->join('storage_addresses', 'storage_addresses.id', 'stock_storages.storage_address_id')->join('locations', 'locations.id', 'storage_addresses.location_id')->where('stock_id', $stock_id)->get();
        // dd($stock_storages);

        try {
            $storage_addresses = StorageAddress::where('location_id', $stock_storages[0]->location_id)->orderby('address', 'asc')->get();
        } catch (Exception $e) {
            $storage_addresses = StorageAddress::where('location_id', 1)->get();
        }

        return view('stock.edit.stocks', compact('stock', 'classifications', 'processes', 'stock_storages', 'locations', 'storage_addresses', 'stock_suppliers'));
    }

    // 発注登録
    public function order_stock($stock_id)
    {
        $stock = Stock::where('id', $stock_id)->first();
        $classifications = Classification::all();
        $processes = Process::all();
        $locations = Location::all();

        $stock_suppliers = StockSupplier::select('stock_suppliers.id as stock_supplier_id', 'stock_suppliers.memo as stock_supplier_memo', 'suppliers.*', 'stock_suppliers.lead_time', 'stock_suppliers.act_flg')->where('stock_id', $stock_id)->join('suppliers', 'suppliers.id', 'stock_suppliers.supplier_id')->get();



        // 在庫状況
        $stock_storages = StockStorage::select('stock_storages.id as stock_storage_id', 'quantity', 'locations.name as location_name', 'address', 'location_id', 'storage_addresses.id as storage_address_id')->join('storage_addresses', 'storage_addresses.id', 'stock_storages.storage_address_id')->join('locations', 'locations.id', 'storage_addresses.location_id')->where('stock_id', $stock_id)->get();
        // dd($stock_storages);

        try {
            $storage_addresses = StorageAddress::where('location_id', $stock_storages[0]->location_id)->orderby('address', 'asc')->get();
        } catch (Exception $e) {
            $storage_addresses = StorageAddress::where('location_id', 1)->get();
        }

        return view('stock.initialOrder.stocks', compact('stock', 'classifications', 'processes', 'stock_storages', 'locations', 'storage_addresses', 'stock_suppliers'));
    }


    public function order_store(Request $request)
    {
        $stock_id = $request->stock_id;
        $quantity = $request->quantity;

        $stock = Stock::find($stock_id);
        $stock_supplier = StockSupplier::select('suppliers.*')->join('suppliers', 'suppliers.id', 'stock_suppliers.supplier_id')->where('stock_id', $stock_id)->first();
        if (!$stock_supplier) {
            Method::errorMsg('先に得意先を選択してください');
            return to_route('stock.edit.stocks', ['stock_id' => $stock_id]);
        }


        $initial_order = new InitialOrder();
        $initial_order->order_date = date('Y-m-d');
        $initial_order->com_no = $stock_supplier->supplier_no;
        $initial_order->com_name = $stock_supplier->name;
        $initial_order->name = $stock->name;
        $initial_order->s_name = $stock->s_name;
        $initial_order->quantity = $quantity;
        $initial_order->order_unit = $stock->solo_unit;
        $initial_order->deli_location = $stock->deli_location;
        $initial_order->save();
    }

    // 全ての発注データを取得
    public function getAllInitialOrders()
    {
        $initial_orders = InitialOrder::where(function ($query) {
            $query->whereNull('receive_flg')
                ->orWhere('receive_flg', 0);
        })->orderBy('order_date', 'desc')->get();

        foreach ($initial_orders as $order) {
            $stock = Stock::where('name', $order->name)
                ->where(function ($query) use ($order) {
                    $query->where('s_name', 'like', "%$order->s_name%")
                        ->orWhere('s_name', $order->s_name);
                })->first();
            if ($stock) {
                $order->img_path = $stock->img_path;
            }
        }

        return response()->json($initial_orders);
    }

    // 発注一覧
    public function initial_orders()
    {
        return Inertia::render('Stock/InitialOrders');
    }
    public function update_initial_order(Request $request)
    {
        $status = 1;
        $msg = "";

        $order_id = $request->order_id;
        $field = $request->field;
        $value = $request->value;

        if (!($order_id && $field)) {
            $status = 0;
        }
        $initial_order = InitialOrder::find($order_id);
        switch ($field) {
            case 'name':
                $initial_order->name = $value;
                break;
            case 's_name':
                $initial_order->s_name = $value;
                break;
                $msg = 'フィールドの値が違います。';
            default:
        }

        // フラグを修正
        if ($initial_order->not_found_flg) {
            $initial_order->not_found_flg = 0;
        }
        $initial_order->save();

        return response()->json(['status' => $status, 'msg' => $msg]);
    }


    public function store_stocks(Request $request)
    {
        $stock_id = $request->stock_id;
        $stock_no = $request->stock_no;
        $name = $request->name;
        $jan_code = $request->jan_code;
        $s_name = $request->s_name;
        $img_path = $request->img_path;
        $price = $request->price;
        $url = $request->url;
        $del_flg = $request->del_flg;
        $purchase_identification_number = $request->purchase_identification_number;
        $solo_unit = $request->solo_unit;
        $org_unit = $request->org_unit;
        $main_unit_flg = $request->main_unit_flg;
        $quantity_per_org = $request->quantity_per_org;
        $classification_id = $request->classification_id;
        $deli_location = $request->deli_location;
        $process_code = $request->process_code;
        $memo = $request->memo;



        $stock = Stock::find($stock_id);
        if (!$stock) {
            $stock = new Stock();
        }
        if ($request->hasFile('upload_file')) {

            $image = $request->file('upload_file');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $img_path = 'storage/' . $image->storeAs('stock', $imageName, 'stock');
        }
        $stock->name = $name;
        $stock->stock_no = $stock_no;
        $stock->s_name = $s_name;
        $stock->jan_code = $jan_code;
        $stock->url = $url;
        $stock->img_path = $img_path;
        $stock->price = $price;
        $stock->purchase_identification_number = $purchase_identification_number;
        $stock->solo_unit = $solo_unit;
        $stock->org_unit = $org_unit;
        $stock->quantity_per_org = $quantity_per_org;
        $stock->main_unit_flg = $main_unit_flg;
        $stock->classification_id = $classification_id;
        $stock->deli_location = $deli_location;
        $stock->process_code = $process_code;
        $stock->memo = $memo;
        $stock->del_flg = $del_flg ?? 0;
        $stock->save();

        Method::msg('success', '完了しました。');
        // dd($stock_id, $stock_no, $name, $jan_code, $s_name, $img_path, $url, $purchase_identification_number, $solo_unit, $org_unit, $main_unit_flg,$quantity_per_org, $classification_id, $deli_location, $process_code, $memo);

        return redirect()->back();
    }

    // 棚卸し
    public function stock_taking(Request $request)
    {
        $storage_address_id = $request->keyword ?? null;
        $storage_address_name = StorageAddress::find($storage_address_id)->address ?? '';
        $storage_addresses = StorageAddress::orderby('address', 'asc')->get();


        $stocks = StockStorage::select('stocks.*', 'stock_storages.quantity')
            ->join('stocks', 'stocks.id', 'stock_storages.stock_id')
            ->join('classifications', 'classifications.id', 'stocks.classification_id')
            ->where('storage_address_id', $storage_address_id)
            ->where('stocks.del_flg', 0)
            ->where('stocks.not_stock_flg', 0)
            ->paginate(20);


        return view('stock.taking.stocks', compact('stocks', 'storage_addresses', 'storage_address_name'));
    }


    // 在庫作成
    public function create_stocks()
    {
        $classifications = Classification::all();
        return view('stock.create.stocks', compact('classifications'));
    }
    // 格納先作成
    public function create_storage_addresses()
    {
        $locations = Location::all();
        foreach ($locations as $location) {
            $storage_address_count = StorageAddress::where('location_id', $location->id)->count();
            $location->address_count = $storage_address_count;
        }

        return view('stock.create.storage_address', compact('locations'));
    }

    // 場所作成
    public function store_location(Request $request)
    {


        $location_name = $request->location_name;

        // 名前が送信されていない場合
        if (!$location_name) {

            return redirect()->back();
        }


        // 既に同じ名前で存在する場合
        $location = Location::where('name', $location_name)->first();
        if ($location) {
            return redirect()->back();
        }


        $location = new Location();
        $location->name = $location_name;
        $location->save();

        return redirect()->back();
    }
    // アドレス作成
    public function store_storage_address(Request $request)
    {

        $location_id = $request->location_id;
        $shelf = $request->shelf;
        $row = $request->row;
        $col = $request->col;
        $sub_row = $request->sub_row;
        // アドレス作成
        $address = $shelf . '-' . $row;
        if ($col) {
            $address = $address . '-' . $col;
        }
        if ($sub_row) {
            $address = $address . '-' . $sub_row;
        }

        // $address = $request->address;

        if (!$location_id) {
            return redirect()->back();
        }

        // 一時的処置
        // if(!$location_id){
        //     $location_id = 2;
        // }
        // StorageAddress新規作成
        $storage_address = new StorageAddress();
        $storage_address->address = $address;
        $storage_address->location_id = $location_id;
        $storage_address->shelf = $shelf;
        $storage_address->row = $row;
        $storage_address->col = $col;
        $storage_address->sub_row = $sub_row;
        $storage_address->save();

        return redirect()->route('stock.storage_addresses.create');
    }

    // 取引先作成
    public function create_suppliers()
    {

        return view('stock.create.suppliers');
    }
    // 取引先編集
    public function supplier_edit() {}


    public function update_stock_storage(Request $request)
    {
        $method = $request->method;
        $stock_storage_id = $request->stock_storage_id;
        $storage_address_id = $request->storage_address_id;

        if (!($method == 'change')) {
            // 同一格納先
            // アドレスもしくは、個数の変更


            $quantity = $request->quantity;
            // dd($quantity, $stock_storage_id);

            if (!$storage_address_id || !$quantity) {


                Method::msg('error', '数量が未入力の可能性があります。');
                return redirect()->back();
            }
            // dd($stock_storage_id);

            $stock_storage = StockStorage::where('id', $stock_storage_id)->first();
            if ($stock_storage) {
                $stock_storage->storage_address_id = $storage_address_id;
                $stock_storage->quantity = $quantity;
                $stock_storage->save();
            }
        } else {
            // 格納先の変更

            $stock_storage = StockStorage::where('id', $stock_storage_id)->first();
            if ($stock_storage) {
                $stock_storage->storage_address_id = $storage_address_id;
                $stock_storage->save();
            }
        }




        Method::msg('success', '更新が完了しました。');




        return redirect()->back();
    }

    public function delete_stock_storage(Request $request)
    {
        $stock_storage_id = $request->stock_storage_id;

        // dd($stock_storage_id);
        $stock_storage = StockStorage::find($stock_storage_id);
        $stock_storage->delete();

        return redirect()->back();
    }

    public function create_stock_storage(Request $request)
    {
        $stock_id = $request->stock_id;
        $storage_address_id = $request->storage_address_id;
        $quantity = $request->quantity;

        if (!($stock_id && $storage_address_id && $quantity)) {
            Method::msg('error', 'アドレスと個数を入力して再度お試しください。');
            return redirect()->back();
        }

        $stock_storage = new StockStorage();
        $stock_storage->stock_id = $stock_id;
        $stock_storage->storage_address_id = $storage_address_id;
        $stock_storage->quantity = $quantity;
        $stock_storage->save();

        Method::msg('success', '在庫保管情報を登録しました。');
        return redirect()->back();
    }

    public function stock_add_supplier(Request $request)
    {
        $stock_id = $request->stock_id;
        $supplier_id = $request->supplier_id;

        if (!($stock_id && $supplier_id)) {
            Method::errorMsg();
            return redirect()->back();
        }
        $stock_supplier = new StockSupplier();
        $stock_supplier->stock_id = $stock_id;
        $stock_supplier->supplier_id = $supplier_id;
        $stock_supplier->save();
        Method::msg('success', '得意先を紐づけました。');
        return redirect()->back();
    }
    public function store_stock_suppliers(Request $request)
    {
        $stock_supplier_id = $request->stock_supplier_id;
        $lead_time = $request->lead_time;
        $memo = $request->memo;

        Method::checkNull($stock_supplier_id);

        $stock_supplier = StockSupplier::where('id', $stock_supplier_id)->first();
        $stock_supplier->lead_time = $lead_time;
        $stock_supplier->memo = $memo;
        $stock_supplier->save();
        // dd($stock_supplier);

        Method::msg('success', '物品得意先情報を更新しました。');
        return redirect()->back();
    }
    public function delete_stock_suppliers(Request $request)
    {
        $stock_supplier_id = $request->stock_supplier_id;

        Method::checkNull($stock_supplier_id);
        $stock_supplier = StockSupplier::find($stock_supplier_id);
        $stock_supplier->delete();

        Method::msg('success', '物品得意先情報を削除しました。');
        return redirect()->back();
    }

    //  滞留品
    public function retained_stocks(Request $request)
    {
        $user_id = $request->user_id;
        if (!$user_id) {


            // ユーザーが設定されていない場合、全員の処遇決定情報を表示する
            $retained_stocks = RetainedStock::select('stocks.id', 'stocks.name', 'stocks.img_path')->join('stocks', 'stocks.id', 'retained_stocks.stock_id')->join('users', 'users.id', 'retained_stocks.user_id')->where('user_id', '!=', 91)->distinct('stocks.id')->get();

            foreach ($retained_stocks as $stock) {
                $retain_lists = RetainedStock::select('users.id as user_id', 'users.name as user_name', 'treats.name as treat_name', 'treats.color as treat_color')->join('users', 'users.id', 'retained_stocks.user_id')->join('treats', 'treats.id', 'retained_stocks.treat_id')->where('stock_id', $stock->id)->get();

                $stock->retain_lists = $retain_lists;
            }


            Method::errorMsg('ユーザーIDが設定されていません。');



            return Inertia::render('Stock/AllRetainStocks', ['retained_stocks' => $retained_stocks]);
        }
        $user_name = User::find($user_id)->name;

        // 処遇が決定したもののリスト
        // ユーザーごと
        $retained_stocks = RetainedStock::select('stocks.*', 'retained_stocks.user_id', 'retained_stocks.treat_id', 'users.name as user_name')->join('stocks', 'stocks.id', 'retained_stocks.stock_id')->join('users', 'users.id', 'retained_stocks.user_id')->where('retained_stocks.user_id', $user_id)->get();



        // 配列に変更
        $retained_ids = $retained_stocks->pluck('id')->toArray();



        // 品証二階に移動させたもののみ取得
        $stocks = StockStorage::select('stocks.*', 'stock_storages.quantity')
            ->join('stocks', 'stocks.id', 'stock_storages.stock_id')
            ->where('stocks.del_flg', 0)
            ->where('storage_address_id', 6)
            ->where('stocks.del_flg', 0)
            ->whereNotIn('stocks.id', $retained_ids)
            ->orderby('updated_at', 'desc')->get();


        return Inertia::render('Stock/RetainStocks', ['user_name' => $user_name, 'user_id' => $user_id, 'stocks' => $stocks, 'retained_stocks' => $retained_stocks]);
    }

    public function store_retained_stocks(Request $request)
    {
        $stock_id = $request->stock_id;
        $user_id = $request->user_id;
        $treat_id = $request->treat_id;


        if (!($stock_id && $user_id && $treat_id)) {
            return;
        }

        DB::beginTransaction();
        try {

            RetainedStock::create([
                'stock_id' => $stock_id,
                'user_id' => $user_id,
                'treat_id' => $treat_id
            ]);

            // 実際に廃棄するタイミングでdel_flgを変更する
            // $stock = Stock::find($stock_id);
            // $stock->del_flg = 1;
            // $stock->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }

        return to_route('stock.retained.stocks', ['user_id' => $user_id]);
    }

    public function store_last_treat_record(Request $request)
    {

        $treat_lists = $request->treat_lists;
        // dd($treat_lists);
        foreach ($treat_lists as $key => $treat) {

            $last_treat_record = new LastTreatRecord();
            $last_treat_record->stock_id = $key;

            switch ($treat) {
                case '1':
                    $treat = '廃棄';
                    break;
                case '2':
                    $treat = '一課引き取り';
                    break;
                case '3':
                    $treat = '二課引き取り';
                    break;
                case '4':
                    $treat = '品証引き取り';
                    break;
                default:
                    $treat = '廃棄';
            }
            $last_treat_record->treat = $treat;
            $last_treat_record->save();
        };

        return redirect()->back();
    }

    // アドレス用紙印刷
    public function print()
    {
        $locations = Location::all();
        $storage_addresses = StorageAddress::orderBy('address', 'asc')->get();

        return Inertia::render('Stock/StorageAddressPrint', ['locations' => $locations, 'storage_addresses' => $storage_addresses]);
    }
}
