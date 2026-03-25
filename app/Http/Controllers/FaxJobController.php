<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FaxJobController extends Controller
{
    public function index()
    {
        $rows = DB::table('fax_parameters')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($row) => $this->formatFaxRow($row))
            ->values();

        return response()->json([
            'success' => true,
            'requests' => $rows,
            'total' => $rows->count(),
        ]);
    }

    public function show(string $id)
    {
        $row = DB::table('fax_parameters')->where('id', $id)->first();
        if (!$row) {
            return response()->json([
                'success' => false,
                'error' => '該当リクエストなし',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'request' => $this->formatFaxRow($row),
        ]);
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'file_url' => ['required', 'string'],
            'fax_number' => ['required', 'string', 'max:20'],
            'request_user' => ['nullable', 'string', 'max:100'],
            'file_name' => ['nullable', 'string', 'max:255'],
            'callback_url' => ['nullable', 'string'],
            'order_destination' => ['nullable', 'string', 'max:100'],
            'initial_order_id' => ['nullable'],
        ]);

        $id = (string) Str::uuid();
        $now = now();

        DB::table('fax_parameters')->insert([
            'id' => $id,
            'file_url' => $payload['file_url'],
            'fax_number' => $payload['fax_number'],
            'status' => 0,
            'created_at' => $now,
            'updated_at' => $now,
            'error_message' => null,
            'converted_pdf_path' => null,
            'request_user' => $payload['request_user'] ?? null,
            'file_name' => $payload['file_name'] ?? null,
            'callback_url' => $payload['callback_url'] ?? null,
            'order_destination' => $payload['order_destination'] ?? null,
        ]);

        if (!empty($payload['initial_order_id'])) {
            DB::table('initial_orders')
                ->where('id', $payload['initial_order_id'])
                ->update(['fax_parameter_id' => $id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'FAX送信リクエストを登録しました',
            'fax_parameter_id' => $id,
            'id' => $id,
            'status' => 'pending',
            'request_user' => $payload['request_user'] ?? null,
            'file_name' => $payload['file_name'] ?? null,
            'callback_url' => $payload['callback_url'] ?? null,
            'order_destination' => $payload['order_destination'] ?? null,
            'fax_number' => $payload['fax_number'],
            'created_at' => $now->toISOString(),
            'updated_at' => $now->toISOString(),
        ]);
    }

    public function updateStatus(Request $request, string $id)
    {
        $payload = $request->validate([
            'status' => ['required', 'integer'],
            'error_message' => ['nullable', 'string'],
        ]);

        $updated = DB::table('fax_parameters')
            ->where('id', $id)
            ->update([
                'status' => $payload['status'],
                'error_message' => $payload['error_message'] ?? null,
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            return response()->json([
                'success' => false,
                'error' => '該当リクエストなし',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'ステータスを更新しました',
        ]);
    }

    public function updateConvertedPdf(Request $request, string $id)
    {
        $payload = $request->validate([
            'converted_pdf_path' => ['required', 'string'],
        ]);

        $updated = DB::table('fax_parameters')
            ->where('id', $id)
            ->update([
                'converted_pdf_path' => $payload['converted_pdf_path'],
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            return response()->json([
                'success' => false,
                'error' => '該当リクエストなし',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => '変換PDFパスを更新しました',
        ]);
    }

    public function retryErrors()
    {
        $count = DB::table('fax_parameters')
            ->where('status', -1)
            ->update([
                'status' => 0,
                'error_message' => null,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => "{$count}件のエラー送信を再送しました",
            'retry_count' => $count,
        ]);
    }

    public function retryById(string $id)
    {
        $count = DB::table('fax_parameters')
            ->where('id', $id)
            ->where('status', -1)
            ->update([
                'status' => 0,
                'error_message' => null,
                'updated_at' => now(),
            ]);

        if ($count === 0) {
            return response()->json([
                'success' => false,
                'error' => '指定IDはエラー状態ではないか、存在しません',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => '再送キューへ戻しました',
        ]);
    }

    public function clearCompleted()
    {
        $count = DB::table('fax_parameters')->where('status', 1)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count}件の完了済み送信履歴を削除しました",
            'deleted_count' => $count,
        ]);
    }

    public function clearAll()
    {
        $count = DB::table('fax_parameters')->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count}件の送信履歴をすべて削除しました",
            'deleted_count' => $count,
        ]);
    }

    public function showInitialOrder(string $id)
    {
        $row = DB::table('initial_orders')
            ->select('id', 'fax_parameter_id')
            ->where('id', $id)
            ->first();

        if (!$row) {
            return response()->json([
                'success' => false,
                'error' => '該当initial_orderなし',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'initial_order' => [
                'id' => $row->id,
                'fax_parameter_id' => $row->fax_parameter_id,
            ],
        ]);
    }

    public function updateInitialOrder(Request $request, string $id)
    {
        $payload = $request->validate([
            'fax_parameter_id' => ['required', 'string', 'size:36'],
        ]);

        $updated = DB::table('initial_orders')
            ->where('id', $id)
            ->update([
                'fax_parameter_id' => $payload['fax_parameter_id'],
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            return response()->json([
                'success' => false,
                'error' => '該当initial_orderなし',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'initial_orders.fax_parameter_idを更新しました',
        ]);
    }

    private function formatFaxRow(object $row): array
    {
        return [
            'id' => $row->id,
            'file_url' => $row->file_url,
            'fax_number' => $row->fax_number,
            'status' => (int) $row->status,
            'created_at' => $this->toIsoOrNull($row->created_at),
            'updated_at' => $this->toIsoOrNull($row->updated_at),
            'error_message' => $row->error_message,
            'converted_pdf_path' => $row->converted_pdf_path,
            'request_user' => $row->request_user,
            'file_name' => $row->file_name,
            'callback_url' => $row->callback_url,
            'order_destination' => $row->order_destination,
        ];
    }

    private function toIsoOrNull($value): ?string
    {
        if (!$value) {
            return null;
        }
        try {
            return \Illuminate\Support\Carbon::parse($value)->toISOString();
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}
