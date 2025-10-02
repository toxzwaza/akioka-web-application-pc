<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\ApprovalFlow;
use App\Models\ApprovalFlowStep;
use App\Models\ApprovalFlowCondition;

// Laravelアプリケーションを起動
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "承認フローSQL実行開始\n";
echo "実行時刻: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n";

try {
    // 既存の承認フローを削除
    echo "1. 既存の承認フローを削除中...\n";
    DB::statement('DELETE FROM approval_flow_conditions');
    DB::statement('DELETE FROM approval_flow_steps');
    DB::statement('DELETE FROM approval_flows');
    echo "   既存データの削除完了\n";

    // SQLファイルを読み込み
    echo "2. SQLファイルを読み込み中...\n";
    $sql = file_get_contents('approval_flows_new_items_additional.sql');
    $statements = explode(';', $sql);
    echo "   SQLファイル読み込み完了\n";

    // SQLステートメントを実行
    echo "3. SQLステートメントを実行中...\n";
    $executedCount = 0;
    $errorCount = 0;

    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && 
            !str_starts_with($statement, '--') && 
            !str_starts_with($statement, 'SELECT') &&
            !str_starts_with($statement, 'DELETE FROM')) {
            
            try {
                $result = DB::statement($statement);
                $executedCount++;
                if (str_starts_with($statement, 'INSERT INTO')) {
                    echo "   INSERT実行: " . substr($statement, 0, 50) . "...\n";
                } elseif (str_starts_with($statement, 'SET @flow_id')) {
                    echo "   SET @flow_id実行\n";
                }
            } catch (Exception $e) {
                $errorCount++;
                echo "   エラー: " . $e->getMessage() . "\n";
                echo "   ステートメント: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }

    echo "   実行完了: {$executedCount}件のステートメントを実行\n";
    if ($errorCount > 0) {
        echo "   エラー: {$errorCount}件のエラーが発生\n";
    }

    // 登録結果を確認
    echo "4. 登録結果を確認中...\n";
    $flows = ApprovalFlow::with(['steps', 'conditions'])->get();
    echo "   登録された承認フロー数: " . $flows->count() . "\n";

    // カテゴリ別の集計
    $newItemFlows = $flows->filter(function($flow) {
        return str_contains($flow->name, '新規品');
    });
    $existingItemFlows = $flows->filter(function($flow) {
        return str_contains($flow->name, '既存品');
    });

    echo "   新規品承認フロー: " . $newItemFlows->count() . "件\n";
    echo "   既存品承認フロー: " . $existingItemFlows->count() . "件\n";

    // 詳細情報をログに出力
    echo "\n5. 詳細情報:\n";
    foreach ($flows as $flow) {
        echo "   ID: {$flow->id}, 名前: {$flow->name}\n";
        echo "      ステップ数: {$flow->steps->count()}, 条件数: {$flow->conditions->count()}\n";
        
        if ($flow->steps->count() > 0) {
            echo "      承認者: ";
            $approvers = $flow->steps->pluck('approver_user_id')->toArray();
            echo implode(', ', $approvers) . "\n";
        }
        echo "\n";
    }

    echo "========================================\n";
    echo "承認フローSQL実行完了\n";
    echo "完了時刻: " . date('Y-m-d H:i:s') . "\n";

} catch (Exception $e) {
    echo "エラーが発生しました: " . $e->getMessage() . "\n";
    echo "スタックトレース:\n" . $e->getTraceAsString() . "\n";
}
