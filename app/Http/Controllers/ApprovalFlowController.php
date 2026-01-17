<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Group;
use App\Models\Position;
use App\Http\Services\ApprovalFlowService;
use App\Http\Services\Helper;

class ApprovalFlowController extends Controller
{
    /**
     * 承認フロー一覧を表示
     */
    public function index()
    {
        $approvalFlows = \App\Models\ApprovalFlow::with(['steps', 'conditions'])->get();
        
        return Inertia::render('Master/ApprovalFlows/Index', [
            'approvalFlows' => $approvalFlows
        ]);
    }

    /**
     * 承認フロー作成フォームを表示
     */
    public function create()
    {
        $users = User::with(['group', 'position'])->get();
        
        return Inertia::render('Master/ApprovalFlows/Create', [
            'users' => $users,
            'conditionTypes' => ApprovalFlowService::getConditionTypes(),
            'operators' => ApprovalFlowService::getOperators()
        ]);
    }

    /**
     * 承認フローを保存
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'conditions' => 'array',
            'steps' => 'array'
        ]);

        ApprovalFlowService::createFlow($request->all());

        return redirect()->route('master.approval-flows.index')
            ->with('success', '承認フローが作成されました。');
    }

    /**
     * 承認フロー詳細を表示
     */
    public function show($id)
    {
        $approvalFlow = \App\Models\ApprovalFlow::with(['steps.approver', 'conditions'])->findOrFail($id);
        
        return Inertia::render('Master/ApprovalFlows/Show', [
            'flow' => $approvalFlow
        ]);
    }

    /**
     * 承認フロー編集フォームを表示
     */
    public function edit($id)
    {
        $approvalFlow = \App\Models\ApprovalFlow::with(['steps', 'conditions'])->findOrFail($id);
        $users = User::with(['group', 'position'])->get();
        
        return Inertia::render('Master/ApprovalFlows/Edit', [
            'flow' => $approvalFlow,
            'users' => $users,
            'conditionTypes' => ApprovalFlowService::getConditionTypes(),
            'operators' => ApprovalFlowService::getOperators()
        ]);
    }

    /**
     * 承認フローを更新
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'conditions' => 'array',
            'steps' => 'array'
        ]);

        $approvalFlow = \App\Models\ApprovalFlow::findOrFail($id);
        ApprovalFlowService::updateFlow($approvalFlow, $request->all());

        return redirect()->route('master.approval-flows.index')
            ->with('success', '承認フローが更新されました。');
    }

    /**
     * 承認フローを削除
     */
    public function destroy($id)
    {
        $approvalFlow = \App\Models\ApprovalFlow::findOrFail($id);
        $approvalFlow->delete();

        return redirect()->route('master.approval-flows.index')
            ->with('success', '承認フローが削除されました。');
    }

    /**
     * 承認フローテストページを表示
     */
    public function test()
    {
        $users = User::with(['group', 'position'])->get();
        $groups = Group::all();
        $positions = Position::all();
        
        return Inertia::render('Master/ApprovalFlows/Test', [
            'users' => $users,
            'groups' => $groups,
            'positions' => $positions,
            'testResult' => session('testResult')
        ]);
    }

    /**
     * 承認フローテストを実行
     */
    public function runTest(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'is_new_item' => 'required|boolean'
        ]);

        $approverIds = ApprovalFlowService::createApprovalFlow(
            $request->price,
            $request->user_id,
            $request->is_new_item ? 1 : 0
        );

        // 承認者IDの配列を承認者の詳細情報に変換
        $approvers = [];
        $approvalFlow = null;
        
        if (!empty($approverIds)) {
            $users = User::whereIn('id', $approverIds)->get()->keyBy('id');
            foreach ($approverIds as $approverId) {
                $user = $users->get($approverId);
                if ($user) {
                    $approvers[] = [
                        'id' => $user->id,
                        'name' => $user->name
                    ];
                }
            }
            
            // 適用された承認フローを取得
            $user = User::find($request->user_id);
            $approvalFlow = \App\Models\ApprovalFlow::getMatchingFlow(
                $user, 
                $request->price, 
                $request->is_new_item
            );
        }

        $user = User::with(['group', 'position'])->find($request->user_id);
        
        return back()->with('testResult', [
            'user' => $user,
            'price' => $request->price,
            'is_new_item' => $request->is_new_item,
            'approvers' => $approvers,
            'approvalFlow' => $approvalFlow ? [
                'id' => $approvalFlow->id,
                'name' => $approvalFlow->name,
                'description' => $approvalFlow->description
            ] : null
        ]);
    }

    /**
     * 承認フロー一括テストページを表示
     */
    public function bulkTest()
    {
        $users = User::with(['group', 'position'])->get();
        $groups = Group::all();
        $positions = Position::all();
        
        return Inertia::render('Master/ApprovalFlows/BulkTest', [
            'users' => $users,
            'groups' => $groups,
            'positions' => $positions
        ]);
    }

    /**
     * 承認フローテストAPI（一括テスト用）
     */
    public function runTestApi(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'is_new_item' => 'required|boolean'
        ]);

        $approverIds = ApprovalFlowService::createApprovalFlow(
            $request->price,
            $request->user_id,
            $request->is_new_item ? 1 : 0
        );

        // 承認者IDの配列を承認者の詳細情報に変換
        $approvers = [];
        $approvalFlow = null;
        
        if (!empty($approverIds)) {
            $users = User::whereIn('id', $approverIds)->get()->keyBy('id');
            foreach ($approverIds as $approverId) {
                $user = $users->get($approverId);
                if ($user) {
                    $approvers[] = [
                        'id' => $user->id,
                        'name' => $user->name
                    ];
                }
            }
            
            // 適用された承認フローを取得
            $user = User::find($request->user_id);
            $approvalFlow = \App\Models\ApprovalFlow::getMatchingFlow(
                $user, 
                $request->price, 
                $request->is_new_item
            );
        }

        return response()->json([
            'success' => true,
            'approvers' => $approvers,
            'approvalFlow' => $approvalFlow ? [
                'id' => $approvalFlow->id,
                'name' => $approvalFlow->name,
                'description' => $approvalFlow->description
            ] : null
        ]);
    }

    /**
     * Helper::createApprovalFlowのテストページを表示
     */
    public function helperTest()
    {
        // 有効な全ユーザーを取得（del_flg = 0）
        $users = User::with(['group', 'position'])
            ->where('del_flg', 0)
            ->orderBy('group_id')
            ->orderBy('position_id')
            ->orderBy('name')
            ->get();
        
        $groups = Group::all();
        $positions = Position::all();
        
        // テスト用の固定金額配列
        $testPrices = [9999, 10001, 49999, 50001, 149999, 150001];
        
        return Inertia::render('Master/ApprovalFlows/HelperTest', [
            'users' => $users,
            'groups' => $groups,
            'positions' => $positions,
            'testPrices' => $testPrices,
            'testResults' => session('testResults')
        ]);
    }

    /**
     * Helper::createApprovalFlowの一括テストを実行
     */
    public function runHelperTest()
    {
        // 有効な全ユーザーを取得（del_flg = 0）
        $users = User::with(['group', 'position'])
            ->where('del_flg', 0)
            ->orderBy('group_id')
            ->orderBy('position_id')
            ->orderBy('name')
            ->get();
        
        // テスト用の固定金額配列
        $testPrices = [9999, 10001, 49999, 50001, 149999, 150001];
        
        // 承認者情報を事前取得（パフォーマンス向上のため）
        $allUserIds = $users->pluck('id')->toArray();
        $allApprovers = User::whereIn('id', $allUserIds)->get()->keyBy('id');
        
        // テスト結果を格納する配列
        $testResults = [];
        
        foreach ($users as $user) {
            $userResults = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'group_id' => $user->group_id,
                'group_name' => $user->group ? $user->group->name : '未設定',
                'position_id' => $user->position_id,
                'position_name' => $user->position ? $user->position->name : '未設定',
                'prices' => []
            ];
            
            foreach ($testPrices as $price) {
                $priceResults = [
                    'price' => $price,
                    'new_item' => [],
                    'existing_item' => []
                ];
                
                // 新規品フラグ = 1（新規品）のテスト
                $newItemApprovers = Helper::createApprovalFlow($price, $user->id, 1);
                $priceResults['new_item'] = $this->convertApproverIdsToNames($newItemApprovers, $allApprovers);
                
                // 新規品フラグ = 0（既存品）のテスト
                $existingItemApprovers = Helper::createApprovalFlow($price, $user->id, 0);
                $priceResults['existing_item'] = $this->convertApproverIdsToNames($existingItemApprovers, $allApprovers);
                
                $userResults['prices'][] = $priceResults;
            }
            
            $testResults[] = $userResults;
        }
        
        return back()->with('testResults', $testResults);
    }

    /**
     * 承認者ID配列を承認者情報配列に変換
     */
    private function convertApproverIdsToNames($approverIds, $allApprovers)
    {
        if (empty($approverIds)) {
            return [];
        }
        
        $approvers = [];
        foreach ($approverIds as $approverId) {
            $approver = $allApprovers->get($approverId);
            if ($approver) {
                $approvers[] = [
                    'id' => $approver->id,
                    'name' => $approver->name,
                    'group_name' => $approver->group ? $approver->group->name : '未設定',
                    'position_name' => $approver->position ? $approver->position->name : '未設定'
                ];
            } else {
                // ユーザーが見つからない場合でもIDを表示
                $approvers[] = [
                    'id' => $approverId,
                    'name' => "ユーザーID: {$approverId} (未検出)",
                    'group_name' => '未設定',
                    'position_name' => '未設定'
                ];
            }
        }
        
        return $approvers;
    }
}
