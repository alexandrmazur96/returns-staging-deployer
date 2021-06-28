<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;

class OrdersToolController extends Controller
{
    public function actionIndex()
    {
        return view('tools.orders.home');
    }
}
