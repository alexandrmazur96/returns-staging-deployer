<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;

class TrackingToolController extends Controller
{
    public function actionIndex()
    {
        return view('tools.tracking.home');
    }
}
