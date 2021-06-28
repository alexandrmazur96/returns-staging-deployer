<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;

class FilesToolController extends Controller
{
    public function actionIndex()
    {
        return view('tools.files.home');
    }
}
