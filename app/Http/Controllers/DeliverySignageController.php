<?php

namespace App\Http\Controllers;

use App\Models\InitialOrder;
use App\Models\DeliverySignageManualItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DeliverySignageController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('DeliverySignage/Manage');
    }

    public function data(): JsonResponse
    {
        return response()->json($this->buildDisplayedItems());
    }

    public function signageData(): JsonResponse
    {
        $items = $this->buildDisplayedItems()
            ->map(function (array $item): array {
                return [
                    'item_type' => $item['item_type'],
                    'item_id' => $item['item_id'],
                    'order_user' => $item['order_user'],
                    'name' => $item['name'],
                    's_name' => $item['s_name'],
                    'display_order' => $item['display_order'],
                ];
            })
            ->values();

        return response()->json($items);
    }

    public function receipt(int $id): JsonResponse
    {
        $order = InitialOrder::find($id);

        if (!$order) {
            return response()->json(['message' => '対象データが見つかりません。'], 404);
        }

        $order->receipt_flg = 1;
        $order->save();

        return response()->json(['message' => '引渡済に更新しました。']);
    }

    public function toggleReceive(int $id): JsonResponse
    {
        $order = InitialOrder::find($id);

        if (!$order) {
            return response()->json(['message' => '対象データが見つかりません。'], 404);
        }

        $order->receive_flg = (int) ($order->receive_flg === 1 ? 0 : 1);
        $order->save();

        return response()->json([
            'message' => '状態を更新しました。',
            'receive_flg' => $order->receive_flg,
        ]);
    }

    public function storeManual(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'order_user' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            's_name' => ['required', 'string', 'max:255'],
        ]);

        $maxManualOrder = (int) DeliverySignageManualItem::max('display_order');
        $maxInitialOrder = (int) InitialOrder::max('delivery_signage_order');
        $payload['display_order'] = max($maxManualOrder, $maxInitialOrder) + 1;

        $item = DeliverySignageManualItem::create($payload);

        return response()->json($item, 201);
    }

    public function updateManual(Request $request, int $id): JsonResponse
    {
        $item = DeliverySignageManualItem::find($id);
        if (!$item) {
            return response()->json(['message' => '対象データが見つかりません。'], 404);
        }

        $payload = $request->validate([
            'order_user' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            's_name' => ['required', 'string', 'max:255'],
        ]);

        $item->update($payload);

        return response()->json($item);
    }

    public function deleteManual(int $id): JsonResponse
    {
        $item = DeliverySignageManualItem::find($id);
        if (!$item) {
            return response()->json(['message' => '対象データが見つかりません。'], 404);
        }

        $item->delete();

        return response()->json(['message' => '削除しました。']);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_type' => ['required', 'string', 'in:initial_order,manual'],
            'items.*.item_id' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['items'] as $index => $item) {
                $order = $index + 1;
                if ($item['item_type'] === 'initial_order') {
                    InitialOrder::where('id', $item['item_id'])->update(['delivery_signage_order' => $order]);
                    continue;
                }

                DeliverySignageManualItem::where('id', $item['item_id'])->update(['display_order' => $order]);
            }
        });

        return response()->json(['message' => '表示順を更新しました。']);
    }

    private function buildUnifiedItems(): Collection
    {
        $initialOrders = InitialOrder::query()
            ->where('receipt_flg', 0)
            ->orderBy('none_storage_flg', 'desc')
            ->orderBy('receive_flg', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get([
                'id',
                'order_user',
                'name',
                's_name',
                'receive_flg',
                'none_storage_flg',
                'delivery_signage_order',
                'order_date',
                'delivery_date',
                'com_name',
                'quantity',
                'order_unit',
            ]);

        $names = $initialOrders->pluck('name')->unique()->filter()->values()->all();
        $stocksByName = collect();
        if ($names !== []) {
            $stocksByName = Stock::query()
                ->whereIn('name', $names)
                ->orderBy('id')
                ->get(['id', 'name', 's_name', 'img_path'])
                ->groupBy('name');
        }

        $initialOrders = $initialOrders->values()->map(function (InitialOrder $order, int $index) use ($stocksByName): array {
            $candidates = $stocksByName->get($order->name, collect());
            $stock = $this->matchStockForInitialOrder($order, $candidates);

            return [
                'item_type' => 'initial_order',
                'item_id' => $order->id,
                'id' => $order->id,
                'order_user' => $order->order_user,
                'name' => $order->name,
                's_name' => $order->s_name,
                'display_order' => $order->delivery_signage_order ?? (100000 + $index),
                'img_path' => $stock?->img_path,
                'found_flg' => $stock ? 0 : 1,
                'receive_flg' => (int) $order->receive_flg,
                'none_storage_flg' => (int) $order->none_storage_flg,
                'order_date' => $order->order_date,
                'delivery_date' => $order->delivery_date,
                'com_name' => $order->com_name,
                'quantity' => $order->quantity,
                'order_unit' => $order->order_unit,
            ];
        });

        $manualItems = DeliverySignageManualItem::query()
            ->orderBy('display_order')
            ->get(['id', 'order_user', 'name', 's_name', 'display_order'])
            ->map(function (DeliverySignageManualItem $item): array {
                return [
                    'item_type' => 'manual',
                    'item_id' => $item->id,
                    'id' => $item->id,
                    'order_user' => $item->order_user,
                    'name' => $item->name,
                    's_name' => $item->s_name,
                    'display_order' => $item->display_order,
                    'img_path' => null,
                    'found_flg' => 0,
                    'receive_flg' => null,
                    'none_storage_flg' => null,
                    'order_date' => null,
                    'delivery_date' => null,
                    'com_name' => null,
                    'quantity' => null,
                    'order_unit' => null,
                ];
            });

        return $initialOrders
            ->concat($manualItems)
            ->sortBy('display_order')
            ->values();
    }

    /**
     * 在庫マスタ突合（StockTabletController と同条件）。
     * 旧実装は行ごとに Stock を取得しており N+1 になっていたため、候補を一括取得して突合する。
     */
    private function matchStockForInitialOrder(InitialOrder $order, Collection $candidates): ?Stock
    {
        if ($candidates->isEmpty()) {
            return null;
        }

        $ordered = $candidates->sortBy('id')->values();
        $target = (string) $order->s_name;

        foreach ($ordered as $stock) {
            if ((string) $stock->s_name === $target) {
                return $stock;
            }
        }

        return $ordered->first();
    }

    private function buildDisplayedItems(): Collection
    {
        return $this->buildUnifiedItems()
            ->filter(function (array $item): bool {
                if ($item['item_type'] === 'manual') {
                    return true;
                }

                return (int) $item['receive_flg'] === 1;
            })
            ->values();
    }
}
