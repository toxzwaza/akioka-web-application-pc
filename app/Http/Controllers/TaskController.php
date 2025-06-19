<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskTransaction;
use App\Models\TaskUpdateCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TaskController extends Controller
{
    //
    public function index()
    {


        return Inertia::render('Task/Index');
    }
    public function getGanttData()
    {
        $user_id_list = [48, 68, 81, 120, 43, 91];
        $gantt_data = [];

        // ユーザー名のマッピング
        $user_names = [
            48 => '中村仁美',
            68 => '岡堂莉子',
            81 => '三谷優月',
            120 => '風早結衣',
            43 => '中原清志',
            91 => '村上飛羽'
        ];

        // ユーザーカラーのマッピング
        $user_colors = [
            48 => 'bg-indigo-500',
            68 => 'bg-blue-500',
            81 => 'bg-green-500',
            120 => 'bg-pink-500',
            43 => 'bg-orange-500',
            91 => 'bg-red-500'
        ];

        foreach ($user_id_list as $user_id) {
            // ガントチャート用データの構築
            $user_tasks = Task::where('user_id', $user_id)
                ->whereDate('created_at', now()->toDateString())
                ->get();

            Log::info("User {$user_id} tasks count: " . $user_tasks->count());

            $gantt_tasks = [];

            foreach ($user_tasks as $task) {
                $task_transactions = TaskTransaction::where('task_id', $task->id)
                    ->whereDate('created_at', now()->toDateString())
                    ->orderBy('created_at', 'asc')
                    ->get();

                Log::info("Task {$task->id} ({$task->name}) transactions count: " . $task_transactions->count());

                $periods = [];
                $start_time = null;

                foreach ($task_transactions as $transaction) {
                    Log::info("Transaction: task_id={$transaction->task_id}, status={$transaction->status}, created_at={$transaction->created_at}");
                    
                    if ($transaction->status == '0') { // 開始
                        $start_time = $transaction->created_at;
                        Log::info("Started task at: {$start_time}");
                    } elseif (($transaction->status == '1' || $transaction->status == '2') && $start_time) { // 停止または完了
                        $end_time = $transaction->created_at;

                        // 時間を小数形式に変換（例：8:30 → 8.5）
                        $start_hour = (float)$start_time->format('H') + (float)$start_time->format('i') / 60;
                        $end_hour = (float)$end_time->format('H') + (float)$end_time->format('i') / 60;

                        $periods[] = [
                            'start' => round($start_hour, 3),
                            'end' => round($end_hour, 3)
                        ];

                        Log::info("Period added: {$start_hour} - {$end_hour}");

                        $start_time = null;
                    }
                }

                // 現在進行中のタスクの場合
                if ($start_time && $task->status == '0') {
                    $start_hour = (float)$start_time->format('H') + (float)$start_time->format('i') / 60;
                    $current_hour = (float)now()->format('H') + (float)now()->format('i') / 60;

                    $periods[] = [
                        'start' => round($start_hour, 3),
                        'end' => round($current_hour, 3)
                    ];

                    Log::info("Current period added: {$start_hour} - {$current_hour}");
                }

                if (!empty($periods)) {
                    $gantt_tasks[] = [
                        'name' => $task->name,
                        'color' => $user_colors[$user_id],
                        'periods' => $periods
                    ];
                    Log::info("Task {$task->name} added to gantt with " . count($periods) . " periods");
                } else {
                    Log::info("Task {$task->name} has no periods, skipping");
                }
            }

            if (!empty($gantt_tasks)) {
                $gantt_data[] = [
                    'name' => $user_names[$user_id],
                    'tasks' => $gantt_tasks
                ];
                Log::info("User {$user_names[$user_id]} added to gantt data");
            } else {
                Log::info("User {$user_names[$user_id]} has no gantt tasks");
            }
        }

        Log::info("Final gantt data: " . json_encode($gantt_data, JSON_PRETTY_PRINT));

        return response()->json($gantt_data);
    }

    public function getData()
    {
        $user_id_list = [48, 68, 81, 120, 43, 91];

        $user_tasks = Task::whereIn('user_id', $user_id_list)
            ->whereIn('status', ['0', '1'])
            ->get()
            ->groupBy('user_id');

        foreach ($user_tasks as $user_id => $tasks) {
            foreach ($tasks as $task) {
                // 着手合計時間
                $task_transaction_times = TaskTransaction::where('task_id', $task->id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                $total_minutes = 0;
                $start_time = null;

                foreach ($task_transaction_times as $transaction) {
                    if ($transaction->status == '0') {
                        $start_time = $transaction->created_at;
                    } elseif ($transaction->status == '1' && $start_time) {
                        $total_minutes += $transaction->created_at->diffInMinutes($start_time);
                        $start_time = null;
                    }
                }

                if ($start_time) {
                    $total_minutes += now()->diffInMinutes($start_time);
                }

                $task->total_minutes = $total_minutes;
            }

            // total_minutesで降順に並べ替え、同じ場合はcreated_atで新しい順に並べ替え
            $user_tasks[$user_id] = $tasks->sortByDesc(function ($task) {
                return [$task->total_minutes, $task->created_at];
            })->values();
        }

        // statusで昇順に並べ替え
        $user_tasks = $user_tasks->map(function ($tasks) {
            return $tasks->sortBy('status')->values();
        });


        $task_transactions = TaskTransaction::select('tasks.name', 'task_transactions.status', 'task_transactions.created_at', 'users.name as user_name', 'users.id as user_id')
            ->join('tasks', 'tasks.id', 'task_transactions.task_id')
            ->join('users', 'users.id', 'tasks.user_id')
            ->orderBy('task_transactions.created_at', 'desc')
            ->whereDate('task_transactions.created_at', now()->toDateString())
            ->get();

        // 検索補助用キーワード
        $search_keywords = Task::select('name as task_name', 'user_id')->distinct('name')->get();


        return response()->json(['user_tasks' => $user_tasks, 'task_transactions' => $task_transactions, 'search_keywords' => $search_keywords]);
    }

    public function store(Request $request)
    {
        $user_id = $request->user_id;
        $task_name = $request->task_name;
        $task_description = $request->task_description;

        $status = true;
        $msg = '';

        try {
            // 既に実行中のタスクがある場合、中断
            $now_task = Task::where('user_id', $user_id)
                ->where('status', '0')
                ->first();
            if ($now_task) {
                // タスクトランザクションデータ作成
                $task_transaction = new TaskTransaction();
                $task_transaction->task_id = $now_task->id;
                $task_transaction->status = '1'; //停止
                $task_transaction->save();

                $now_task->status = '1';
                $now_task->save();
            }

            // タスク作成
            $task = new Task();
            $task->user_id = $user_id;
            $task->name = $task_name;
            $task->description = $task_description;
            $task->save();

            // タスクトランザクションデータ作成
            $task_transaction = new TaskTransaction();
            $task_transaction->task_id = $task->id;
            $task_transaction->status = '0'; //進行中
            $task_transaction->save();

            $this->changeUpdateCheck($user_id);

            $msg = 'タスクを作成しました';
        } catch (\Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'msg' => $msg]);
    }

    public function update(Request $request)
    {
        $task_id = $request->task_id;
        $flg = $request->flg;

        $status = true;

        try {

            $task = Task::find($task_id);

            if ($task->status == 0) { // 進行中の場合完了

                if ($flg == 'stop') {
                    $task->status = 1;

                    $task_transaction = new TaskTransaction();
                    $task_transaction->task_id = $task_id;
                    $task_transaction->status = 1; //未着手
                    $task_transaction->save();
                } else {
                    $task->status = '2';

                    // タスクトランザクションデータ作成
                    $task_transaction = new TaskTransaction();
                    $task_transaction->task_id = $task_id;
                    $task_transaction->status = 2; //完了
                    $task_transaction->save();
                }
            } else if ($task->status == 1) { // 未着手の場合進行中


                // 現在進行中のタスクを未着手に変更
                $now_task = Task::where('user_id', $task->user_id)
                    ->where('status', 0)
                    ->first();
                if ($now_task) {
                    $now_task->status = 1;
                    $now_task->save();

                    // タスクトランザクションデータ作成
                    $task_transaction = new TaskTransaction();
                    $task_transaction->task_id = $now_task->id;
                    $task_transaction->status = 1; //未着手
                    $task_transaction->save();
                }

                // 未着手タスクを再開
                $task->status = 0;
                // タスクトランザクションデータ作成
                $task_transaction = new TaskTransaction();
                $task_transaction->task_id = $task_id;
                $task_transaction->status = 0; //進行中
                $task_transaction->save();
            }

            $task->save();

            // 更新チェックをリセット
            $this->changeUpdateCheck($task->user_id);


            $msg = 'タスクを更新しました';
        } catch (\Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status, 'msg' => $msg ]);
    }

    public function update_value(Request $request)
    {
        $task_id = $request->task_id;
        $value = $request->value;
        $flg = $request->flg;

        $status = true;

        try {
            $task = Task::find($task_id);

            if ($flg == "task_name") {
                $task->name = $value;
            } else if ($flg == "description") {
                $task->description = $value;
            }
            $task->save();
        } catch (\Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }

    public function delete(Request $request) {}

    public function update_check(Request $request)
    {
        $user_id = $request->user_id;

        $update_flg = false;

        $task_update_check = TaskUpdateCheck::where('user_id', $user_id)->where('update_flg', 1)->first();

        if ($task_update_check) {
            $update_flg = true;
            $task_update_check->update_flg = 0;
            $task_update_check->save();
        }

        return response()->json(['update_flg' => $update_flg]);
    }

    public function changeUpdateCheck($user_id)
    {
        $updateChecks = TaskUpdateCheck::where('user_id', '!=', $user_id)->get();
        foreach ($updateChecks as $updateCheck) {
            $updateCheck->update_flg = 1;
            $updateCheck->save();
        }
    }

    public function getCompleteTasks()
    {
        $user_id_list = [48, 68, 81, 120, 43, 91];

        $complete_tasks = Task::select('tasks.id as task_id', 'tasks.name as task_name', 'users.name as user_name', 'tasks.created_at', 'tasks.updated_at', 'tasks.user_id as user_id')
            ->join('users', 'users.id', '=', 'tasks.user_id')
            ->whereIn('user_id', $user_id_list)
            ->where('tasks.status', '2')
            ->whereDate('tasks.updated_at', now()->toDateString())
            ->orderBy('tasks.created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        $csvData = [];

        foreach ($complete_tasks as $user_id => $tasks) {
            foreach ($tasks as $task) {
                $task_transaction_times = TaskTransaction::where('task_id', $task->task_id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                $total_minutes = 0;
                $start_time = null;

                foreach ($task_transaction_times as $transaction) {
                    if ($transaction->status == '0') {
                        $start_time = $transaction->created_at;
                    } elseif (($transaction->status == '1' || $transaction->status == '2') && $start_time) {
                        $total_minutes += $transaction->created_at->diffInMinutes($start_time);
                        $start_time = null;
                    }
                }

                if ($start_time) {
                    $total_minutes += now()->diffInMinutes($start_time);
                }

                $csvData[] = [
                    'task_id' => $task->task_id,
                    'task_name' => $task->task_name,
                    'user_name' => $task->user_name,
                    'user_id' => $task->user_id,
                    'created_at' => $task->created_at->format('Y/m/d H:i:s'),
                    'updated_at' => $task->updated_at->format('Y/m/d H:i:s'),
                    'total_minutes' => $total_minutes,
                ];
            }
        }

        return response()->json($csvData);
    }

    public function export()
    {
        $csvData = $this->getCompleteTasks()->getData();

        $filename = "tasks_export_" . date('Ymd_His') . ".csv";
        $handle = fopen($filename, 'w');

        // Shift-JISで保存するためにmb_convert_encodingを使用
        fputcsv($handle, mb_convert_encoding(['タスクID', 'タスク名', '実行者', '作成日時', '完了日時', '合計作業時間'], 'SJIS-win', 'UTF-8'));

        foreach ($csvData as $row) {
            fputcsv($handle, mb_convert_encoding((array) $row, 'SJIS-win', 'UTF-8'));
        }

        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }
}
