<?php

namespace App\Http\Controllers;

use App\Models\InitialOrder;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OrderUnitMaintenanceController extends Controller
{
    /**
     * 発注単位整備
     * 在庫の発注単位（stocks.solo_unit）と発注実績の単位（initial_orders.order_unit）を
     * 比較する一覧を表示する。
     * ※ カラムコメント上は org_unit が「発注単位」だが、画面（Stocks/Show）および
     *   発注作成処理（OrderRequestController::createInitialOrder）では solo_unit が
     *   「発注単位」として扱われているため、本画面も solo_unit を対象とする。
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $unit_status = $request->unit_status; // null: すべて / mismatch: 不一致のみ / unset: 発注単位未設定
        $order_unit = $request->order_unit;   // 実績単位で絞り込み
        $include_no_orders = filter_var($request->include_no_orders, FILTER_VALIDATE_BOOLEAN);

        $query = Stock::select(
            'stocks.id',
            'stocks.stock_no',
            'stocks.name',
            'stocks.s_name',
            'stocks.org_unit',
            'stocks.solo_unit'
        )
            ->where('stocks.del_flg', 0)
            ->orderBy('stocks.name', 'asc');

        // デフォルトは発注実績のある在庫のみ（比較対象が存在するもの）
        if (!$include_no_orders) {
            $query->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('initial_orders')
                    ->whereColumn('initial_orders.stock_id', 'stocks.id');
            });
        }

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('stocks.name', 'like', '%' . $keyword . '%')
                    ->orWhere('stocks.s_name', 'like', '%' . $keyword . '%')
                    ->orWhere('stocks.stock_no', 'like', '%' . $keyword . '%');
            });
        }

        if ($unit_status === 'mismatch') {
            // 実績単位のうち、在庫の発注単位と異なるものが1件でも存在する
            $query->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('initial_orders')
                    ->whereColumn('initial_orders.stock_id', 'stocks.id')
                    ->whereNotNull('initial_orders.order_unit')
                    ->where('initial_orders.order_unit', '<>', '')
                    ->whereRaw("NOT (initial_orders.order_unit <=> COALESCE(NULLIF(stocks.solo_unit, ''), NULL))");
            });
        } elseif ($unit_status === 'unset') {
            $query->where(function ($q) {
                $q->whereNull('stocks.solo_unit')->orWhere('stocks.solo_unit', '');
            });
        }

        if ($order_unit) {
            $query->whereExists(function ($q) use ($order_unit) {
                $q->select(DB::raw(1))
                    ->from('initial_orders')
                    ->whereColumn('initial_orders.stock_id', 'stocks.id')
                    ->where('initial_orders.order_unit', $order_unit);
            });
        }

        $stocks = $query->paginate(50)->withQueryString();

        // 表示ページ分の発注実績単位を集計して付与
        $stock_ids = collect($stocks->items())->pluck('id');
        $unit_records = InitialOrder::whereIn('stock_id', $stock_ids)
            ->whereNotNull('order_unit')
            ->where('order_unit', '<>', '')
            ->groupBy('stock_id', 'order_unit')
            ->selectRaw('stock_id, order_unit, COUNT(*) as order_count, MAX(order_date) as last_order_date')
            ->orderByDesc('order_count')
            ->get()
            ->groupBy('stock_id');

        $stocks->getCollection()->transform(function ($stock) use ($unit_records) {
            $stock->order_units = ($unit_records[$stock->id] ?? collect())->values();
            return $stock;
        });

        // 全体の実績単位一覧（表記ゆれ確認・絞り込み・入力候補用）
        $all_units = InitialOrder::whereNotNull('order_unit')
            ->where('order_unit', '<>', '')
            ->whereNotNull('stock_id')
            ->groupBy('order_unit')
            ->selectRaw('order_unit, COUNT(*) as order_count, COUNT(DISTINCT stock_id) as stock_count')
            ->orderByDesc('order_count')
            ->get();

        return Inertia::render('Stock/OrderUnitMaintenance/Index', [
            'stocks' => $stocks,
            'all_units' => $all_units,
            'filters' => [
                'keyword' => $keyword,
                'unit_status' => $unit_status,
                'order_unit' => $order_unit,
                'include_no_orders' => $include_no_orders,
            ],
        ]);
    }

    /**
     * 在庫の発注単位（stocks.solo_unit）を一括で上書きする
     */
    public function bulkUpdate(Request $request)
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.stock_id' => 'required|integer|exists:stocks,id',
                'items.*.solo_unit' => 'required|string|max:255',
            ]);

            DB::transaction(function () use ($validated) {
                foreach ($validated['items'] as $item) {
                    Stock::where('id', $item['stock_id'])
                        ->update(['solo_unit' => trim($item['solo_unit'])]);
                }
            });

            return response()->json([
                'status' => true,
                'message' => count($validated['items']) . '件の発注単位を更新しました。',
                'updated_count' => count($validated['items']),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('OrderUnitMaintenance bulkUpdate error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => '発注単位の更新に失敗しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
