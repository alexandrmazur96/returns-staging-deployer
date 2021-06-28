<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Tools\ApiToolController;
use App\Http\Controllers\Tools\FilesToolController;
use App\Http\Controllers\Tools\OrdersToolController;
use App\Http\Controllers\Tools\ReturnsToolController;
use App\Http\Controllers\Tools\TestsToolController;
use App\Http\Controllers\Tools\TrackingToolController;
use App\Http\Controllers\VersionChangerController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes(
    [
        'login' => true,
        'logout' => true,
        'register' => false,
        'reset' => false,
        'confirm' => false,
        'verify' => false,
    ]
);

Route::get('/', [HomeController::class, 'actionIndex']);

Route::get('/home', [HomeController::class, 'actionIndex'])
    ->name('home');

Route::get('/deployer', [HomeController::class, 'actionDeployer'])->name('deployer');

Route::get('/version-changer', [VersionChangerController::class, 'actionIndex'])
    ->name('version-changer');

Route::get('/fetch-branches/{repository}', [HomeController::class, 'actionFetchBranches'])
    ->name('fetch-branches');

Route::get('/deploy-branch/{repository}/{branch}', [HomeController::class, 'actionDeployBranch'])
    ->name('deploy-branch');

Route::prefix('tool')->group(static function () {
    Route::get('/returns', [ReturnsToolController::class, 'actionIndex'])->name('returns-tool');
    Route::get('/orders', [OrdersToolController::class, 'actionIndex'])->name('orders-tool');
    Route::get('/tracking', [TrackingToolController::class, 'actionIndex'])->name('tracking-tool');
    Route::get('/files', [FilesToolController::class, 'actionIndex'])->name('files-tool');
    Route::get('/tests', [TestsToolController::class, 'actionIndex'])->name('tests-tool');
    Route::get('/api', [ApiToolController::class, 'actionIndex'])->name('api-tool');
});
