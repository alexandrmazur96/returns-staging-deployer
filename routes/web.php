<?php

use App\Http\Controllers\HomeController;
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
        'verify' => false
    ]
);

Route::get('/', [HomeController::class, 'actionIndex']);

Route::get('/home', [HomeController::class, 'actionIndex'])
    ->name('home');

Route::get('/fetch-branches/{repository}', [HomeController::class, 'actionFetchBranches'])
    ->name('fetch-branches');

Route::get('/deploy-branch/{repository}/{branch}', [HomeController::class, 'actionDeployBranch'])
    ->name('deploy-branch');
