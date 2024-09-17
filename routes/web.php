<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductAjaxController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::resource('products',ProductAjaxController::class);