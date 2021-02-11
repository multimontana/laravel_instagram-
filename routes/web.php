<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstagramController;

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

Route::group(
    ['middleware' => 'cors'],
    function () {
        Route::get('/{id?}', [InstagramController::class, 'index']);
        Route::post('/username', [InstagramController::class, 'getUserInfo']);
    }
);
