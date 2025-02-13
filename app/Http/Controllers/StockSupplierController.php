<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockSupplierController extends Controller
{
    //
    public function index()
    {
        $suppliers = Supplier::orderby('updated_at', 'desc')->paginate(20);

        return view('stock.suppliers', compact('suppliers'));
    }
    public function create()
    {
        return Inertia::render('Stock/Supplier/Create');
    }

    public function store(Request $request)
    {
        $status = true;
        $supplier_id = $request->supplier_id;

        $supplier_no = $request->supplier_no;
        $name = $request->name;
        $rub_name = $request->rub_name;
        $tel = $request->tel;
        $fax = $request->fax;
        $address = $request->address;
        $invoice_registration_number = $request->invoice_registration_number;
        $memo = $request->memo;

        try {
            if ($supplier_id) {
                // 編集の場合
                $supplier = Supplier::find($supplier_id);
                $supplier->supplier_no = $supplier_no;
                $supplier->name = $name;
                $supplier->rub_name = $rub_name;
                $supplier->tel = $tel;
                $supplier->fax = $fax;
                $supplier->address = $address;
                $supplier->invoice_registration_number = $invoice_registration_number;
                $supplier->memo = $memo;
                $supplier->save();
            } else {
                // 新規登録の場合
                if ($name) {
                    $supplier = new Supplier();
                    $supplier->supplier_no = $supplier_no;
                    $supplier->name = $name;
                    $supplier->rub_name = $rub_name;
                    $supplier->tel = $tel;
                    $supplier->fax = $fax;
                    $supplier->address = $address;
                    $supplier->invoice_registration_number = $invoice_registration_number;
                    $supplier->memo = $memo;
                    $supplier->save();
                }
            }
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json($status);
    }

    public function edit($supplier_id)
    {
        $supplier = Supplier::find($supplier_id);

        return Inertia::render('Stock/Supplier/Create', ['supplier' => $supplier, 'edit' => true]);
    }
}
