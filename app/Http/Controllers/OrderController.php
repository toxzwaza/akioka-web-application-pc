<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\ApprovalAcceptFlow;
use App\Models\ConsumOrder;
use App\Models\ObjectRequest;
use App\Models\RequestAcceptFlow;
use App\Services\Method;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    public function index()
    {
        // 消耗品発注依頼取得
        $consumOrders = ConsumOrder::select('consum_orders.*', 'users.name as user_name', 'stocks.name as stock_name','stocks.img_path')->join('users', 'users.id', 'consum_orders.user_id')->join('stocks', 'stocks.id', 'consum_orders.stock_id')->orderby('created_at', 'desc')->get();

        return view('order.index', compact('consumOrders'));
    }
    public function consumOrders()
    {
        // 消耗品発注依頼リスト

        // 消耗品発注依頼取得
        $consumOrders = ConsumOrder::select('consum_orders.*', 'users.name as user_name', 'stocks.name as stock_name','stocks.img_path','stocks.price','stocks.main_unit_flg','stocks.solo_unit','stocks.org_unit','stocks.quantity_per_org','stocks.id as stock_id','stocks.url')->join('users', 'users.id', 'consum_orders.user_id')->join('stocks', 'stocks.id', 'consum_orders.stock_id')->orderby('del_flg','asc')->orderby('order_flg','asc')->orderby('created_at', 'asc')->paginate(20);

        return view('order.consumOrders',compact('consumOrders'));
    }
    public function print_consumOrders(Request $request){
        $consumOrder_id = $request->consumOrder_id;
        $consumOrder = ConsumOrder::select('consum_orders.*', 'users.name as user_name', 'stocks.name as stock_name','stocks.s_name as s_name','stocks.price','stocks.main_unit_flg','stocks.solo_unit','stocks.org_unit','stocks.quantity_per_org','stocks.id as stock_id','stocks.url','stocks.deli_location','suppliers.name as supplier_name','suppliers.tel','suppliers.fax')->join('users', 'users.id', 'consum_orders.user_id')->join('stocks', 'stocks.id', 'consum_orders.stock_id')->join('stock_suppliers','stocks.id','stock_suppliers.stock_id')->join('suppliers','suppliers.id','stock_suppliers.supplier_id')->find($consumOrder_id);
        // dd($consumOrder);


        return view('order.print.consumOrder',compact('consumOrder'));
    }

    // 消耗品発注編集
    public function store_consumOrders(Request $request){
        $consumOrder_id = $request->consumOrder_id;
        $limit = $request->limit;
        // dd($limit);
        $consumOrder = ConsumOrder::find($consumOrder_id);
        $consumOrder->limit = $limit;
        $consumOrder->save();

        return redirect()->back();

    }

    // 消耗品注文完了
    public function complete_consumOrders(Request $request){
        $consumOrder_id = $request->consumOrder_id;
        if(!$consumOrder_id){
            Method::errorMsg();
            return to_route('order.consumOrders');
        }

        $consumOrder = ConsumOrder::find($consumOrder_id);
        $consumOrder->order_flg = 1;
        $consumOrder->save();
        Method::msg('success','発注完了を登録しました。');
        return to_route('order.consumOrders');
    }
    // 消耗品注文削除
    public function delete_consumOrders(Request $request){
        $consumOrder_id = $request->consumOrder_id;
        if(!$consumOrder_id){
            Method::errorMsg();
            return to_route('order.consumOrders');
        }
        $consumOrder = ConsumOrder::find($consumOrder_id);
        $consumOrder->del_flg = 1;
        $consumOrder->save();
        Method::msg('success','発注情報を削除しました。');
        return to_route('order.consumOrders');

    } 

    // 承認依頼リスト表示
    public function already_requests(Request $request)
    {
        $approvals = Approval::select('approvals.*', 'est_documents.est_doc_path', 'users.name as user_name')->join('est_documents', 'est_documents.id', 'approvals.est_document_id')->join('users', 'users.id', 'approvals.user_id')->orderby('created_at', 'desc')->get();
        foreach ($approvals as $approval) {
            $approval_flows = ApprovalAcceptFlow::where('approval_id', $approval->id)->get();

            $approval->approval_flows = $approval_flows;
        }

        $object_requests = ObjectRequest::select('object_requests.*', 'users.name as user_name', 'stocks.name as stock_name', 'stocks.price')->join('users', 'users.id', 'object_requests.user_id')->join('stocks', 'stocks.id', 'object_requests.stock_id')->orderby('created_at', 'desc')->get();
        foreach ($object_requests as $object_request) {
            $request_flows = RequestAcceptFlow::where('request_id', $object_request->id)->get();
            $object_request->request_flows = $request_flows;
        }

        return view('order.already_requests', compact('approvals', 'object_requests'));
    }

    public function already_orders()
    {
        // 稟議取得
        $approvals = Approval::select('approvals.*', 'users.name as user_name')->join('users', 'users.id', 'approvals.user_id')
            ->where('status', '=', 1)->where('order_status', '=', 0)->orderby('created_at', 'desc')->get();



        // 物品依頼
        $object_requests = ObjectRequest::select('object_requests.*', 'stocks.name as stock_name', 'stocks.img_path', 'stocks.price', 'stocks.url', 'users.name as user_name')->join('stocks', 'stocks.id', 'object_requests.stock_id')->join('users', 'users.id', 'object_requests.user_id')->where('status', '=', 1)->where('order_status', '=', 0)->orderby('created_at', 'desc')->get();
        // 承認フローが承認されていない場合、トリム
        foreach ($object_requests as $key => $object_request) {

            $flows = RequestAcceptFlow::where('request_id', '=', $object_request->id)->get();
            $is_valid = true;
            foreach ($flows as $flow) {
                if ($flow->status !== 1) {
                    $is_valid = false;
                    break;
                }
            }
            if (!$is_valid) {
                unset($object_requests[$key]);
            }
        }

        return view('order.already_orders', compact('approvals', 'object_requests'));
    }

    public function approval_judge(Request $request)
    {
        $id = $request->id;
        $user_id = $request->user_id ?? null;

        $approval = Approval::select('approvals.*', 'users.name as user_name', 'est_documents.est_doc_path')->join('est_documents', 'est_documents.id', 'approvals.est_document_id')->join('users', 'users.id', 'approvals.user_id')->find($id);
        if ($approval->status) {
            Method::msg('info', '既に承認されています。');
            return redirect()->back();
        }
        $approval_flows = ApprovalAcceptFlow::select('approval_accept_flows.*', 'users.name as user_name', 'users.id as user_id')->where('approval_id', $approval->id)->join('users', 'users.id', 'approval_accept_flows.user_id')->get();
        $approval->approval_flows = $approval_flows;

        return view('order.approval_judge', compact('approval', 'user_id'));
    }
    public function object_request_judge(Request $request)
    {
        $id = $request->id;

        return view('order.object_request_judge');
    }

    public function create_orders()
    {
    }
}
