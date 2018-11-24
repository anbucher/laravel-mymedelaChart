<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $uploadedFile = $request->file('excel_file');
        $filename = "import.xlsx";
  
        Storage::disk('local')->putFileAs(
            'files/',
            $uploadedFile,
            $filename
        );

      return back();
    }
}
