<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;
//Controller
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\RoomTypeController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\TransitionController;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\FloorController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\EmployeeRoleController;
use App\Http\Controllers\Api\RoomUsingController;
use App\Http\Controllers\Api\RoomUsingGuestController;
use App\Http\Controllers\Api\RoomUsingServiceController;
use App\Http\Controllers\Api\Categories\DiaChinhController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderRoomController;
use App\Http\Middleware\AuthenticateEmployee;
use App\Http\Controllers\Api\OrderHistoryController;

Route::group([
    'prefix' => 'system',
], function () {
    Route::get('auth/token', [AuthController::class, 'authToken']);
    Route::post('register', [AuthController::class, 'store']);
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware([AuthenticateEmployee::class])->group(function () {
        // Khách sạn
        Route::group([
            'prefix' => 'hotel'
        ], function () {
            Route::get('get-list', [HotelController::class, 'index']);
            Route::get('options', [HotelController::class, 'getCombobox']);
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
            Route::get('get-list', [RoomController::class, 'index']);
            Route::get('options', [RoomController::class, 'getCombobox']);
            Route::post('', [RoomController::class, 'store']);
            Route::get('', [RoomController::class, 'show']);
            Route::put('', [RoomController::class, 'update']);
            Route::delete('', [RoomController::class, 'destroy']);
        });

        // Phòng
        Route::group([
            'prefix' => 'room-type'
        ], function () {
            Route::get('get-list', [RoomTypeController::class, 'index']);
            Route::get('options', [RoomTypeController::class, 'getCombobox']);
            Route::post('', [RoomTypeController::class, 'store']);
            Route::get('', [RoomTypeController::class, 'show']);
            Route::put('', [RoomTypeController::class, 'update']);
            Route::delete('', [RoomTypeController::class, 'destroy']);
        });

        // Giao dịch
        // Transition
        Route::group([
            'prefix' => 'transition'
        ], function () {
            Route::get('get-list', [TransitionController::class, 'index']);
            Route::post('', [TransitionController::class, 'store']);
            Route::get('', [TransitionController::class, 'show']);
            Route::put('', [TransitionController::class, 'update']);
            Route::delete('', [TransitionController::class, 'destroy']);
        });

        // Sử dụng PT HTTP chuẩn restful api
        // Guest
        Route::group([
            'prefix' => 'guest'
        ], function () {
            Route::get('get-list', [GuestController::class, 'index']);
            Route::get('options', [GuestController::class, 'getCombobox']);
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
            Route::get('options', [FloorController::class, 'getCombobox']);
            Route::post('', [FloorController::class, 'store']);
            Route::get('', [FloorController::class, 'show']);
            Route::put('', [FloorController::class, 'update']);
            Route::delete('', [FloorController::class, 'destroy']);
        });

        //EmployeeRole
        Route::group([
            'prefix' => 'employee-role'
        ], function () {
            Route::get('get-list', [EmployeeRoleController::class, 'index']);
            Route::post('', [EmployeeRoleController::class, 'store']);
            Route::get('', [EmployeeRoleController::class, 'show']);
            Route::put('', [EmployeeRoleController::class, 'update']);
            Route::delete('', [EmployeeRoleController::class, 'destroy']);
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

        Route::group([
            'prefix' => 'room-using'
        ], function () {
            Route::get('get-list', [RoomUsingController::class, 'index']);
            Route::post('', [RoomUsingController::class, 'store']);
            Route::get('', [RoomUsingController::class, 'show']);
            Route::put('', [RoomUsingController::class, 'update']);
            Route::delete('', [RoomUsingController::class, 'destroy']);
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
            Route::post('calculate-fee', [RoomUsingServiceController::class, 'calculateServiceFee']);
        });

        //room-using-guest
        Route::group([
            'prefix' => 'room-using-guest'
        ], function () {
            Route::get('get-list', [RoomUsingGuestController::class, 'index']);
            Route::post('', [RoomUsingGuestController::class, 'store']);
            Route::get('', [RoomUsingGuestController::class, 'show']);
            Route::put('', [RoomUsingGuestController::class, 'update']);
            Route::delete('', [RoomUsingGuestController::class, 'destroy']);
        });

        // Service
        Route::group([
            'prefix' => 'service'
        ], function () {
            Route::get('get-list', [ServiceController::class, 'index']);
            Route::get('options', [ServiceController::class, 'getCombobox']);
            Route::post('', [ServiceController::class, 'store']);
            Route::get('', [ServiceController::class, 'show']);
            Route::put('', [ServiceController::class, 'update']);
            Route::delete('', [ServiceController::class, 'destroy']);
        });

        Route::group([
            'prefix' => 'categories/diachinh'
        ], function () {
            Route::get('options', [DiaChinhController::class, 'getCombobox']);
        });

        Route::group([
            'prefix' => 'order-room'
        ], function () {
            Route::post('', [OrderRoomController::class, 'store']);
            Route::put('', [OrderRoomController::class, 'update']);
            Route::post('calculator', [OrderRoomController::class, 'calulatorPrice']);
        });

        Route::group([
            'prefix' => 'order-history'
        ], function () {
            Route::get('', [OrderHistoryController::class, 'index']);
        });
    });
});
