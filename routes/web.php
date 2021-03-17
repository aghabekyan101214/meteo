<?php

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
Auth::routes([
    'register' => false
]);

Route::group(['middleware' => 'auth'], function () {
    Route::get("/",  "TableController@index")->name("main");
    Route::get("meteo",  "TableController@index")->name("metar");
    Route::post("export-to-excel",  "TableController@export_to_excel")->name("export_to_excel");
});

