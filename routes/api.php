<?php

use App\Http\Controllers\CallController;
use App\Http\Controllers\MegafonController;
use Illuminate\Support\Facades\Route;


Route::apiResource('calls', CallController::class)->only(['index', 'store']);

Route::group(['prefix' => 'megafon'], function () {
    Route::post('history', [MegafonController::class, 'history']);
});
