<?php

namespace App\Http\Services;

use App\Models\ApprovalFlow;
use App\Models\User;

class ApprovalFlowService
{
    /**
     * 承認フローを作成（既存のHelper.phpのcreateApprovalFlowを置き換え）
     */
    public static function createApprovalFlow($price, $user_id, $new_flg = 0)
    {
        $user = User::find($user_id);
        if (!$user) {
            return [];
        }

        // 承認不要の条件をチェック
        if (self::isApprovalNotRequired($user, $new_flg)) {
            return [];
        }

        // 条件にマッチする承認フローを取得
        $approvalFlow = ApprovalFlow::getMatchingFlow($user, $price, $new_flg == 1);
        
        if (!$approvalFlow) {
            return [];
        }

        // 承認者IDのリストを返す（既存の承認フロー登録機能との互換性を保つ）
        return $approvalFlow->steps->pluck('approver_user_id')->toArray();
    }

    /**
     * 承認が不要かどうかをチェック
     */
    private static function isApprovalNotRequired($user, $new_flg)
    {
        // 役員・統括部（役員）・顧問
        if (in_array($user->group_id, [8, 10, 11])) {
            return true;
        }

        // 既存品の品証・技術
        if (!$new_flg && in_array($user->group_id, [1, 2])) {
            return true;
        }

        return false;
    }

    /**
     * 承認フローを作成（管理画面用）
     */
    public static function createFlow($data)
    {
        $flow = ApprovalFlow::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'is_active' => true
        ]);

        // 条件を保存
        if (isset($data['conditions'])) {
            foreach ($data['conditions'] as $condition) {
                $flow->conditions()->create([
                    'condition_type' => $condition['condition_type'],
                    'operator' => $condition['operator'],
                    'condition_value' => json_encode($condition['condition_value'])
                ]);
            }
        }

        // ステップを保存
        if (isset($data['steps'])) {
            foreach ($data['steps'] as $step) {
                $flow->steps()->create([
                    'step_order' => $step['step_order'],
                    'approver_user_id' => $step['approver_user_id'],
                    'is_required' => $step['is_required']
                ]);
            }
        }

        return $flow;
    }

    /**
     * 承認フローを更新（管理画面用）
     */
    public static function updateFlow($flow, $data)
    {
        $flow->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'is_active' => $data['is_active'] ?? true
        ]);

        // 既存の条件とステップを削除
        $flow->conditions()->delete();
        $flow->steps()->delete();

        // 新しい条件を保存
        if (isset($data['conditions'])) {
            foreach ($data['conditions'] as $condition) {
                $flow->conditions()->create([
                    'condition_type' => $condition['condition_type'],
                    'operator' => $condition['operator'],
                    'condition_value' => json_encode($condition['condition_value'])
                ]);
            }
        }

        // 新しいステップを保存
        if (isset($data['steps'])) {
            foreach ($data['steps'] as $step) {
                $flow->steps()->create([
                    'step_order' => $step['step_order'],
                    'approver_user_id' => $step['approver_user_id'],
                    'is_required' => $step['is_required']
                ]);
            }
        }

        return $flow;
    }

    /**
     * 条件タイプの選択肢を取得
     */
    public static function getConditionTypes()
    {
        return [
            'position' => '役職',
            'group' => '部署',
            'price_min' => '最低金額',
            'price_max' => '最高金額',
            'is_new_item' => '新規品フラグ'
        ];
    }

    /**
     * 演算子の選択肢を取得
     */
    public static function getOperators()
    {
        return [
            '=' => '等しい',
            '>' => 'より大きい',
            '<' => 'より小さい',
            '>=' => '以上',
            '<=' => '以下',
            'in' => '含む',
            'not_in' => '含まない'
        ];
    }
}
