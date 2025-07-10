<?php

namespace App\Http\Controllers;

use App\Models\OrderRequest;
use Exception;
use Illuminate\Http\Request;
use App\Http\Services\Helper;
use App\Models\OrderRequestApproval;
use App\Models\User;
use Inertia\Inertia;

class AcceptController extends Controller
{
    public function index(Request $request)
    {
        $order_request_id = $request->order_request_id;

        // 承認用画面
        $order_request = OrderRequest::select(
            'order_requests.id as order_request_id',
            'order_requests.quantity',
            'order_requests.price',
            'order_requests.user_id',
            'stocks.name as stock_name',
            'stocks.s_name as stock_s_name',
            'stocks.img_path as stock_img_path',
            'suppliers.name as supplier_name',
            'suppliers.tel as supplier_tel',
            'suppliers.fax as supplier_fax',
            'users.name as user_name',
            'request_user.name as request_user_name'
        )
            ->join('stocks', 'stocks.id', '=', 'order_requests.stock_id')
            ->join('suppliers', 'suppliers.id', '=', 'order_requests.supplier_id')
            ->join('users', 'users.id', '=', 'order_requests.user_id')
            ->leftJoin('users as request_user', 'request_user.id', '=', 'order_requests.request_user_id')
            ->where('order_requests.id', $order_request_id)
            ->first();

        return Inertia::render('Stock/Accept/Index', ['order_request' => $order_request]);
    }

    // 承認依頼送信
    public function sendAccept(Request $request)
    {
        $status = true;
        $msg = "";
        $accept_flg = 0;

        try {
            $order_request_id = $request->order_request_id;
            $user_id = $request->user_id; //注文者

            $contain_approvals = $request->contain_approvals;
            $file_path = $request->file_path;

            // 複数の承認依頼の場合
            if (count($contain_approvals) > 0 && $file_path) {
                foreach ($contain_approvals as $contain_approval) {
                    $order_request = OrderRequest::find($contain_approval);
                    $order_request->accept_flg = 1; //承認待ち状態
                    $order_request->user_id = $user_id;
                    $order_request->file_path = $file_path;
                    $order_request->save();

                    // システム自動発注依頼の場合は承認依頼者を発注依頼者とする
                    if ($order_request->request_user_id == 117) {
                        $order_request->request_user_id = $user_id;
                    }
                    $order_request->save();

                    // 承認フローを作成
                    // 金額と承認依頼者を元に作成
                    // 今後は、依頼者を元に承認フローを作成
                    $approval_list = Helper::createApprovalFlow($order_request->calc_price, $order_request->request_user_id, $order_request->new_flg);
                    // $approval_list = Helper::createApprovalFlow($order_request->calc_price, $user_id);

                    if (count($approval_list) > 0) {
                        foreach ($approval_list as $key => $approval_user_id) {

                            $order_request_approval = new OrderRequestApproval();
                            $order_request_approval->user_id = $approval_user_id; //承認者
                            $order_request_approval->order_request_id = $order_request_id; //承認依頼ID
                            $order_request_approval->status = ($key === 0) ? 0 : null; //最初のユーザーを承認待ち
                            $order_request_approval->final_flg = ($key === count($approval_list) - 1) ? 1 : 0; //最後のユーザーを最終承認者
                            $order_request_approval->save();

                            if ($order_request_approval->status === 0) {
                                $url = "https://akioka.cloud/accept/order-request?user_id=" . $order_request_approval->user_id;

                                $title = "在庫管理システムからの通知です。";
                                $message = "承認依頼を受け付けました。\n\n以下のURLから承認を行ってください。";

                                Helper::createNotifyQueue($title, $message, $url, [$order_request_approval->user_id]);
                            }
                        }
                    } else {

                        // 承認必要なし
                        $order_request->accept_flg = 2;
                        $order_request->save();
                    }
                }
            } else {
                $order_request = OrderRequest::find($order_request_id);
                $order_request->accept_flg = 1; //承認待ち状態
                $order_request->user_id = $user_id;

                // システム自動発注依頼の場合は承認依頼者を発注依頼者とする
                if ($order_request->request_user_id == 117) {
                    $order_request->request_user_id = $user_id;
                }
                $order_request->save();

                // 承認フローを作成
                // 金額と承認依頼者を元に作成
                // 今後は、依頼者を元に承認フローを作成
                $approval_list = Helper::createApprovalFlow($order_request->calc_price, $order_request->request_user_id, $order_request->new_flg);
                // $approval_list = Helper::createApprovalFlow($order_request->calc_price, $user_id);
                if (count($approval_list) > 0) {
                    foreach ($approval_list as $key => $approval_user_id) {

                        $order_request_approval = new OrderRequestApproval();
                        $order_request_approval->user_id = $approval_user_id; //承認者
                        $order_request_approval->order_request_id = $order_request_id; //承認依頼ID
                        $order_request_approval->status = ($key === 0) ? 0 : null; //最初のユーザーを承認待ち
                        $order_request_approval->final_flg = ($key === count($approval_list) - 1) ? 1 : 0; //最後のユーザーを最終承認者
                        $order_request_approval->save();

                        if ($order_request_approval->status === 0) {
                            $url = "https://akioka.cloud/accept/order-request?user_id=" . $order_request_approval->user_id;

                            $title = "在庫管理システムからの通知です。";
                            $message = "承認依頼を受け付けました。\n\n以下のURLから承認を行ってください。";

                            Helper::createNotifyQueue($title, $message, $url, [$order_request_approval->user_id]);
                        }
                    }
                    // 承認送信
                    $accept_flg = 1;
                } else {

                    $order_request->accept_flg = 2;
                    $order_request->save();
                    $accept_flg = 2; //承認必要なし
                }
            }
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }


        return response()->json(['status' => $status, 'msg' => $msg, 'accept_flg' => $accept_flg]);
    }

    public function reNotify(Request $request)
    {
        $status = true;
        $order_request_id = $request->order_request_id;
        $user_id = $request->user_id;

        try {
            $url = "https://akioka.cloud/accept/order-request?user_id=" . $user_id;

            $order_request = OrderRequest::select('stocks.name as stock_name', 'stocks.s_name as stock_s_name')->join('stocks', 'stocks.id', '=', 'order_requests.stock_id')->find($order_request_id);
            $message = $order_request->stock_name . ' ' . $order_request->stock_s_name . "の承認依頼を受け付けました。\n\n以下のURLから承認を行ってください。";

            $title = "在庫管理システムからの通知です。";

            Helper::createNotifyQueue($title, $message, $url, [$user_id]);
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }

    public function skipAccept(Request $request)
    {
        $status = true;
        $order_request_id = $request->order_request_id;
        $user_id = $request->user_id;

        try {
            $order_request = OrderRequest::find($order_request_id);
            $order_request->accept_flg = 2;
            $order_request->user_id = $user_id;
            $order_request->save();
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }

    public function store(Request $request)
    {
        $status = true;
        $order_request_id = $request->order_request_id;
        $accept_flg = $request->accept_flg;



        try {
            if ($accept_flg) {
                $order_request = OrderRequest::find($order_request_id);
                $order_request->accept_flg = $accept_flg;
                $order_request->save();
            }

            $order_request = OrderRequest::select('order_requests.accept_flg', 'stocks.name as stock_name', 'stocks.s_name as stock_s_name')->join('stocks', 'stocks.id', '=', 'order_requests.stock_id')
                ->where('order_requests.id', $order_request_id)
                ->first();

            // 通知者リスト
            $notify_list = ['村上飛羽', '三谷優月', '岡堂莉子', '中村仁美'];

            if ($accept_flg == 2) {
                $message = $order_request->stock_name . ' ' . $order_request->stock_s_name . "の発注依頼が承認されました。\n\n以下のURLから発注を行ってください。";
            } else if ($accept_flg == 3) {
                $message = "承認が拒否されました。";
            }

            foreach ($notify_list as $notify) {
                $user = User::where('name', $notify)->first();
                Helper::sendNotify([$user->email], $message, route('stock.order_requests', ['user_id' => $user->id]));
            }
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }

    public function sendReject(Request $request)
    {
        $status = true;
        $order_request_id = $request->order_request_id;


        try {
            $order_request = OrderRequest::select('order_requests.*', 'stocks.name as stock_name', 'stocks.s_name as stock_s_name', 'users.name as request_user_name')
                ->join('stocks', 'stocks.id', '=', 'order_requests.stock_id')
                ->join('users', 'users.id', '=', 'order_requests.request_user_id')
                ->find($order_request_id);

            // 再依頼待ち
            $order_request->accept_flg = 4;
            $order_request->save();

            // 承認フローを取得
            $reject_order_request_approval = OrderRequestApproval::select('order_request_approvals.*', 'users.name as user_name')
                ->where('order_request_id', $order_request_id)
                ->where('status', 2)
                ->join('users', 'users.id', 'order_request_approvals.user_id')
                ->first();

            $accept_order_request_approvals = OrderRequestApproval::where('order_request_id', $order_request_id)
                ->where('status', 1)
                ->get();

            $title = "承認が却下されました。";
            $message = "以下の物品依頼が却下されました。依頼内容を改め、再依頼してください。\n\n品名: $order_request->stock_name\n品番: $order_request->stock_s_name\n依頼者: $order_request->request_user_name\n\n拒否者: $reject_order_request_approval->user_name\n拒否理由: $reject_order_request_approval->comment";

            if (count($accept_order_request_approvals) > 0) {
                foreach ($accept_order_request_approvals as $accept_order_request_approval) {
                    // 承認者にTEAMSで通知
                    Helper::createNotifyQueue($title, $message, '', [$accept_order_request_approval->user_id]);
                }
                $msg = "Teamsメッセージ送信完了";
            } else {
                            // 依頼者にデバイスメッセージで通知
            if (Helper::createDeviceMessage(
                2,
                $order_request->device_id, //to:依頼端末
                null,
                $order_request->request_user_id, //to:依頼者
                $reject_order_request_approval->user_id, //from:拒否者
                $message
            )) {
                $msg = "デバイスメッセージ送信完了";
            }
            }
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'msg' => $msg]);
    }

    public function changeEstimate(Request $request)
    {
        $status = true;
        $order_request_id = $request->order_request_id;
        $user_id = $request->user_id;

        try {
            $order_request = OrderRequest::find($order_request_id);
            $order_request->accept_flg = 5;
            $order_request->user_id = $user_id;
            $order_request->save();
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }
}
