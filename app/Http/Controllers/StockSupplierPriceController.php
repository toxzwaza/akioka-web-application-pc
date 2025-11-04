<?php

namespace App\Http\Controllers;

use App\Models\StockSupplierPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockSupplierPriceController extends Controller
{
    /**
     * 手配先価格を登録
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'stock_id' => 'required|exists:stocks,id',
                'stock_supplier_id' => 'required|exists:stock_suppliers,id',
                'price' => 'required|numeric|min:0',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            ]);

            $price = StockSupplierPrice::create($validated);

            return response()->json([
                'status' => true,
                'message' => '価格を登録しました。',
                'data' => $price,
            ]);
        } catch (\Exception $e) {
            Log::error('StockSupplierPrice store error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => '価格登録に失敗しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 手配先価格を更新
     */
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:stock_supplier_prices,id',
                'price' => 'required|numeric|min:0',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
                'active_flg' => 'integer|in:0,1,2',
            ]);

            $price = StockSupplierPrice::findOrFail($validated['id']);
            $price->update($validated);

            return response()->json([
                'status' => true,
                'message' => '価格を更新しました。',
                'data' => $price,
            ]);
        } catch (\Exception $e) {
            Log::error('StockSupplierPrice update error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => '価格更新に失敗しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 手配先価格を削除
     */
    public function destroy(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:stock_supplier_prices,id',
            ]);

            $price = StockSupplierPrice::findOrFail($validated['id']);
            $price->delete();

            return response()->json([
                'status' => true,
                'message' => '価格を削除しました。',
            ]);
        } catch (\Exception $e) {
            Log::error('StockSupplierPrice destroy error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => '価格削除に失敗しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 在庫の手配先価格一覧を取得
     */
    public function index(Request $request)
    {
        try {
            $stockId = $request->input('stock_id');
            $stockSupplierId = $request->input('stock_supplier_id');

            $query = StockSupplierPrice::with(['stock', 'stockSupplier']);

            if ($stockId) {
                $query->where('stock_id', $stockId);
            }

            if ($stockSupplierId) {
                $query->where('stock_supplier_id', $stockSupplierId);
            }

            $prices = $query->orderBy('start_date', 'desc')->get();

            return response()->json([
                'status' => true,
                'data' => $prices,
            ]);
        } catch (\Exception $e) {
            Log::error('StockSupplierPrice index error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'データ取得に失敗しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 有効フラグを切り替え
     * 0: 無効 ⇔ 1: 有効 を切り替え
     * 2: 適用済み の場合は 1: 有効 に変更
     */
    public function toggleActive(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:stock_supplier_prices,id',
            ]);

            $price = StockSupplierPrice::findOrFail($validated['id']);
            
            // 状態の切り替え
            if ($price->active_flg == 0) {
                $price->active_flg = 1; // 無効 → 有効
            } elseif ($price->active_flg == 1) {
                $price->active_flg = 0; // 有効 → 無効
            } elseif ($price->active_flg == 2) {
                $price->active_flg = 1; // 適用済み → 有効
            }
            
            $price->save();

            return response()->json([
                'status' => true,
                'message' => '有効フラグを更新しました。',
                'data' => $price,
            ]);
        } catch (\Exception $e) {
            Log::error('StockSupplierPrice toggleActive error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => '更新に失敗しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
