<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;

class ApiToolController extends Controller
{
    public function actionIndex()
    {
        return view('tools.api.home');
    }
}
