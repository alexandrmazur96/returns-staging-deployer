<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;

class VersionChangerController extends Controller
{
    public function actionIndex(): Renderable
    {
        return view('version-changer')
            ->with('irCurrentVersion', '20201212151530')
            ->with('irCurrentVersionReadable', 'sample')
            ->with('irVersions', [
                ['raw' => '20201212151530', 'readable' => 'sample 1'],
                ['raw' => '20201212151520', 'readable' => 'sample 2'],
                ['raw' => '20201212151510', 'readable' => 'sample 3'],
            ]);
    }
}
