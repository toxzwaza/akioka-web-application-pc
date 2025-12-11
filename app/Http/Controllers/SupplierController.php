<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    //
    //
    public function index(Request $request)
    {
        $name = $request->name;
        $rub_name = $request->rub_name;
        $tel = $request->tel;
        $fax = $request->fax;
        $p_code = $request->p_code;
        $address = $request->address;

        $query = Supplier::orderby('updated_at', 'desc');

        if ($name) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($rub_name) {
            $query->where('rub_name', 'like', "%{$rub_name}%");
        }

        if ($tel) {
            $query->where('tel', 'like', "%{$tel}%");
        }

        if ($fax) {
            $query->where('fax', 'like', "%{$fax}%");
        }

        if ($p_code) {
            $query->where('p_code', 'like', "%{$p_code}%");
        }

        if ($address) {
            $query->where('address', 'like', "%{$address}%");
        }

        $suppliers = $query->paginate(20)->withQueryString();

        return Inertia::render('Stock/Supplier/Index', [
            'suppliers' => $suppliers,
            'name' => $name,
            'rub_name' => $rub_name,
            'tel' => $tel,
            'fax' => $fax,
            'p_code' => $p_code,
            'address' => $address,
        ]);
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
