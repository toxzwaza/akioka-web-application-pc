<?php

namespace App\Http\Controllers;

use App\Models\Signage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SignageController extends Controller
{
    //
    public function index(){

        $signages = Signage::all();


        return Inertia::render('Signage/Index', ['signages' => $signages]);
    }

    public function store(Request $request){
        $request->validate([
            'file' => 'required|mimes:pdf',
        ]);

        $file = $request->file('file');
        $timestamp = now()->timestamp;
        $fileName = $timestamp . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('pdf', $fileName, 'public');
        
        // サイネージ用DBに追加
        $signage = new Signage();
        $signage->file_name = $fileName;
        $signage->save();

        return redirect()->route('signage.home');
    }

    public function show($id)
    {
        $signage = Signage::findOrFail($id);
        return Inertia::render('Signage/Show', [
            'file_name' => $signage->file_name
        ]);
    }
}
