<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;

class ReturnsToolController extends Controller
{
    public function actionIndex()
    {
        return view('tools.returns.home');
    }
}
