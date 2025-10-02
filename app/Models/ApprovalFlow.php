<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalFlow extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * 承認フローのステップを取得
     */
    public function steps()
    {
        return $this->hasMany(ApprovalFlowStep::class)->orderBy('step_order');
    }

    /**
     * 承認フローの条件を取得
     */
    public function conditions()
    {
        return $this->hasMany(ApprovalFlowCondition::class);
    }

    /**
     * 条件にマッチする承認フローを取得
     */
    public static function getMatchingFlow($user, $price, $isNewItem)
    {
        $flows = self::with(['steps.approver', 'conditions'])->where('is_active', true)->get();

        foreach ($flows as $flow) {
            if ($flow->matchesConditions($user, $price, $isNewItem)) {
                return $flow;
            }
        }

        return null;
    }

    /**
     * 条件にマッチするかチェック
     */
    public function matchesConditions($user, $price, $isNewItem)
    {
        foreach ($this->conditions as $condition) {
            if (!$this->checkCondition($condition, $user, $price, $isNewItem)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 個別条件をチェック
     */
    private function checkCondition($condition, $user, $price, $isNewItem)
    {
        switch ($condition->condition_type) {
            case 'position':
                return $this->compareValue($user->position_id, $condition->operator, $condition->condition_value);
            case 'group':
                return $this->compareValue($user->group_id, $condition->operator, $condition->condition_value);
            case 'price_min':
                return $this->compareValue($price, '>=', $condition->condition_value);
            case 'price_max':
                return $this->compareValue($price, '<', $condition->condition_value);
            case 'is_new_item':
                return $this->compareValue($isNewItem ? 'true' : 'false', $condition->operator, $condition->condition_value);
            default:
                return true;
        }
    }

    /**
     * 値の比較
     */
    private function compareValue($actual, $operator, $expected)
    {
        switch ($operator) {
            case '=':
                return $actual == $expected;
            case '>':
                return $actual > $expected;
            case '<':
                return $actual < $expected;
            case '>=':
                return $actual >= $expected;
            case '<=':
                return $actual <= $expected;
            case 'IN':
                $values = array_map('trim', explode(',', $expected));
                return in_array($actual, $values);
            default:
                return true;
        }
    }
}
