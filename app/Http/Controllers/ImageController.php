<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class ImageController extends Controller
{
    public function show($path)
    {
        if (Storage::disk('protected')->exists($path)) {
            $file = Storage::disk('protected')->get($path);
            $type = Storage::disk('protected')->mimeType($path);

            return Response::make($file, 200)->header("Content-Type", $type);
        }

        return response()->json(['error' => 'Image not found'], 404);
    }
}
