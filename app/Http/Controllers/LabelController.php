<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class LabelController extends Controller
{
    public function index()
    {
        return Inertia::render('Tools/LabelDesignerKonva');
    }
}