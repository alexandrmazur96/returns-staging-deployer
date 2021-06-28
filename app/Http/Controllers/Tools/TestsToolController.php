<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;

class TestsToolController extends Controller
{
    public function actionIndex()
    {
        return view('tools.tests.home');
    }
}
