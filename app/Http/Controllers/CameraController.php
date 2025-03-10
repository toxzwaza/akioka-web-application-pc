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
        $directory = public_path('videos');
        $mp4Files = [];

        if (is_dir($directory)) {
            if ($dh = opendir($directory)) {
                while (($file = readdir($dh)) !== false) {
                    if (pathinfo($file, PATHINFO_EXTENSION) == 'mp4') {
                        $mp4Files[filemtime($directory . '/' . $file)] = $file;
                    }
                }
                closedir($dh);
            }
        }

        krsort($mp4Files);
        $mp4Files = array_values($mp4Files);

        return response()->json($mp4Files);
    }
}
