<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CameraController extends Controller
{
    //
    public function index() {
        return Inertia::render('Stock/CameraArchive');
    }


    public function getCameraMovies()
    {
        $directory = '\\\\192.168.210.91\\Videos';
        $mp4Files = [];

        if (is_dir($directory)) {
            if ($dh = opendir($directory)) {
                while (($file = readdir($dh)) !== false) {
                    if (pathinfo($file, PATHINFO_EXTENSION) == 'mp4') {
                        $mp4Files[] = $file;
                    }
                }
                closedir($dh);
            }
        }

        return response()->json($mp4Files);
    }
}
