<?php

use App\Http\Controllers\GeneralController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatientController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test_cron_job', function () {
    return view('cron_test');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::post('/fetch_tokens', [HomeController::class, 'fetchToken'])->name('homex');

Route::get('/fitbit',  [GeneralController::class,'fitbitRedirect']);
Route::post('/create_patient', [PatientController::class,'store'])->name('create_patient');
Route::post('/update_patient', [PatientController::class,'update'])->name('update_patient');
Route::get('/set_data/{type}/{name}/{value}', [GeneralController::class,'setData']);

