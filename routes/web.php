<?php

use App\Http\Controllers\Admin\TaxController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::name('admin.')->prefix('admin')->group(function () {
    Route::resource('taxes', TaxController::class);
});
