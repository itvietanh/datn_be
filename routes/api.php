<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Controller
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\TransitionController;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\FloorController;
use App\Http\Controllers\Api\RoleController;

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

    // Giao dịch
    // Transition 
    Route::group([
        'prefix' => 'transition'
    ], function () {
        Route::get('get-list', [TransitionController::class, 'index']);
        Route::post('', [TransitionController::class, 'store']);
        Route::get('{uuid}', [TransitionController::class, 'show']);
        Route::put('{uuid}', [TransitionController::class, 'update']);
        Route::delete('{uuid}', [TransitionController::class, 'destroy']);
    });

    // Sử dụng PT HTTP chuẩn restful api
    // Guest
    Route::group([
        'prefix' => 'guest'
    ], function () {
        Route::get('get-list', [GuestController::class, 'index']);
        Route::post('', [GuestController::class, 'store']);
        Route::get('', [GuestController::class, 'show']);
        Route::put('', [GuestController::class, 'update']);
        Route::delete('', [GuestController::class, 'destroy']);
    });

    // Floor
    Route::group([
        'prefix' => 'floor'
    ], function () {
        Route::get('get-list', [FloorController::class, 'index']);
        Route::post('', [FloorController::class, 'store']);
        Route::get('', [FloorController::class, 'show']);
        Route::put('', [FloorController::class, 'update']);
        Route::delete('', [FloorController::class, 'destroy']);
    });
    // role
    Route::group([
        'prefix' => 'role'
    ], function () {
        Route::get('get-list', [RoleController::class, 'index']);
        Route::post('', [RoleController::class, 'store']);
        Route::get('', [RoleController::class, 'show']);
        Route::put('', [RoleController::class, 'update']);
        Route::delete('', [RoleController::class, 'destroy']);
    });
});
