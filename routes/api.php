<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Controller
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\TransitionController;

Route::group([
    'prefix' => 'system'
], function () {
    // Khách sạn
    Route::group([
        'prefix' => 'hotel'
    ], function () {
        Route::get('get-list', [HotelController::class, 'index']);
        Route::post('', [HotelController::class, 'store']);
        Route::get('', [HotelController::class, 'show']);
        Route::put('', [HotelController::class, 'update']);
        Route::delete('', [HotelController::class, 'destroy']);
    });

    // Phòng
    Route::group([
        'prefix' => 'room'
    ], function () {
        Route::get('', [RoomController::class, 'index']);
    });

    // Transition 
    Route::group([
        'prefix' => 'transition'
    ], function () {
        Route::get('', [TransitionController::class, 'index']);
        Route::post('', [TransitionController::class, 'store']);
        Route::get('{uuid}', [TransitionController::class, 'show']);
        Route::put('{uuid}', [TransitionController::class, 'update']);
        Route::delete('{uuid}', [TransitionController::class, 'destroy']);
    });
});
