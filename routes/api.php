<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Controller
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\RoomUsingGuestController;
use App\Http\Controllers\Api\RoomUsingServiceController;

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

    // Nhân viên
    Route::group([
        'prefix' => 'employee'
    ], function () {
        Route::get('get-list', [EmployeeController::class, 'index']);
        Route::post('', [EmployeeController::class, 'store']);
        Route::get('', [EmployeeController::class, 'show']);
        Route::put('', [EmployeeController::class, 'update']);
        Route::delete('', [EmployeeController::class, 'destroy']);
    });

    // Phòng
    Route::group([
        'prefix' => 'room'
    ], function () {
        Route::get('', [RoomController::class, 'index']);
    });

    //service
    Route::group([
        'prefix' => 'service'
    ], function () {
        Route::get('get-list', [RoomUsingServiceController::class, 'index']);
        Route::post('', [RoomUsingServiceController::class, 'store']);
        Route::get('{uuid}', [RoomUsingServiceController::class, 'show']);
        Route::put('{uuid}', [RoomUsingServiceController::class, 'update']);
        Route::delete('{uuid}', [RoomUsingServiceController::class, 'destroy']);
    });

    // Phòng sử dụng dịch vụ (Lmaf service trước mới đúng cchuws)
    Route::group([
        'prefix' => 'room-using-service'
    ], function () {
        Route::get('get-list', [RoomUsingServiceController::class, 'index']);
        Route::post('', [RoomUsingServiceController::class, 'store']);
        Route::get('', [RoomUsingServiceController::class, 'show']);
        Route::put('', [RoomUsingServiceController::class, 'update']);
        Route::delete('', [RoomUsingServiceController::class, 'destroy']);
    });

    Route::group([
        'prefix' => 'room-using-guest'
    ], function () {
        Route::get('get-list', [RoomUsingGuestController::class, 'index']);
        Route::post('', [RoomUsingGuestController::class, 'store']);
        Route::get('', [RoomUsingGuestController::class, 'show']);
        Route::put('', [RoomUsingGuestController::class, 'update']);
        Route::delete('', [RoomUsingGuestController::class, 'destroy']);
    });
});
