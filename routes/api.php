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
use App\Http\Controllers\Api\GuestAccountsController;
use App\Http\Controllers\Api\Categories\DiaChinhController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderRoomController;
use App\Http\Middleware\AuthenticateEmployee;
use App\Http\Controllers\Api\OrderHistoryController;
use App\Http\Controllers\Api\OverdueRoomsUsingController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ServiceStatisticsController;
use App\Http\Controllers\ServiceStatisticController;
use App\Http\Controllers\Api\EmployeeStatisticsController;
use App\Http\Controllers\Api\GuestStatisticsController;
use App\Http\Controllers\Api\TransitionStatisticsController;
use App\Http\Controllers\Api\HomeHotelController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\RoomtypeStatisticsController;

Route::group([
    'prefix' => 'system',
], function () {
    Route::get('auth/token', [AuthController::class, 'authToken']);
    Route::post('register', [AuthController::class, 'store']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::middleware([AuthenticateEmployee::class])->get('auth/profile', [AuthController::class, 'getProfile']);

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
            Route::put('{uuid}', [RoomController::class, 'updateRoomStatus']);
        });

        // Kiểu phòng
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
        Route::group([], function () {
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

        // Route::group([
        //     'prefix' => 'guest-accounts'
        // ], function () {
        //     Route::get('get-list', [GuestAccountsController::class, 'index']);
        //     Route::get('options', [GuestAccountsController::class, 'getCombobox']);
        //     Route::post('', [GuestAccountsController::class, 'store']);
        //     Route::get('', [GuestAccountsController::class, 'show']);
        //     Route::put('', [GuestAccountsController::class, 'update']);
        //     Route::delete('', [GuestAccountsController::class, 'destroy']);
        // });

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
            Route::get('get-list', [RoomUsingController::class, 'getOverdueRooms']);
            // Route::get('/rooms', [RoomUsingController::class, 'getOverdueRooms']);
            Route::post('', [RoomUsingController::class, 'store']);
            Route::get('', [RoomUsingController::class, 'show']);
            Route::put('', [RoomUsingController::class, 'update']);
            Route::delete('', [RoomUsingController::class, 'destroy']);
            Route::put('/room-using/payment/{uuid}', [RoomUsingController::class, 'updateRoomUsingPayment']);
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
            Route::post('insert-guest', [GuestController::class, 'inset-guest']);
            Route::get('', [RoomUsingGuestController::class, 'show']);
            Route::put('', [RoomUsingGuestController::class, 'update']);
            Route::delete('', [RoomUsingGuestController::class, 'destroy']);
        });

        // Service
        Route::group([
            'prefix' => 'service'
        ], function () {
            Route::get('get-list', [ServiceController::class, 'index']);
            Route::get('get-list-service', [ServiceController::class, 'getListService']);
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

        /** Đặt phòng */
        Route::group([
            'prefix' => 'order-room'
        ], function () {
            Route::post('', [OrderRoomController::class, 'store']);
            Route::put('', [OrderRoomController::class, 'update']);
            Route::post('calculator', [OrderRoomController::class, 'calulatorPrice']);
            Route::get('over-time', [OrderRoomController::class, 'handleOverTime']);
            // Route::post('search-rooms', [OrderRoomController::class, 'searchRooms']);
            Route::post('room-change-fee', [OrderRoomController::class, 'handleRoomChange']);
            Route::get('search-rooms', [OrderRoomController::class, 'handleSearchRooms']);
            Route::get('ru-service', [OrderRoomController::class, 'handleGetListRu']);
            /** Import excel list guest */
            Route::group([
                'prefix' => 'import'
            ], function () {
                Route::post('list-guest', [OrderRoomController::class, 'importDataGuest']);
            });
        });

        /** Lịch sử đặt phòng */
        Route::group([
            'prefix' => 'order-history'
        ], function () {
            Route::get('', [OrderHistoryController::class, 'index']);
        });

        /** Danh sách phòng quá hạn*/
        Route::group([
            'prefix' => 'room-using-overdue'
        ], function () {
            Route::get('', [OverdueRoomsUsingController::class, 'index']);
        });

        Route::group([
            'prefix' => 'menu'
        ], function () {
            Route::get('get-list', [MenuController::class, 'index']);
            Route::post('', [MenuController::class, 'store']);
            Route::get('', [MenuController::class, 'show']);
            Route::put('', [MenuController::class, 'update']);
            Route::delete('', [MenuController::class, 'destroy']);
        });

        Route::group(['prefix' => 'statistic'], function () {
            /** Thống kê dịch vụ */
            Route::group([
                'prefix' => 'service'
            ], function () {
                Route::get('/total-revenue', [ServiceStatisticsController::class, 'totalRevenue']);
                Route::get('/service-usage-count', [ServiceStatisticsController::class, 'serviceUsageCount']);
                Route::get('/monthly-revenue', [ServiceStatisticsController::class, 'monthlyRevenue']);
                Route::get('/daily-revenue', [ServiceStatisticsController::class, 'dailyRevenue']);
                Route::get('/weekly-revenue', [ServiceStatisticsController::class, 'weeklyRevenue']);
                Route::get('/all', [ServiceStatisticsController::class, 'allStatistics']); // Route để lấy tất cả thống kê
            });

            /** Thống kê khách hàng */
            Route::group([
                'prefix' => 'guest'
            ], function () {
                Route::get('/total-guests', [GuestStatisticsController::class, 'totalGuests']);
                Route::get('/new-guests-this-month', [GuestStatisticsController::class, 'newGuestsThisMonth']);
                Route::get('/active-guests', [GuestStatisticsController::class, 'activeGuests']);
                Route::get('/all', [GuestStatisticsController::class, 'allStatistics']); // Route để lấy tất cả thống kê khách hàng
            });

            /** Thống kê giao dịch */
            Route::group([
                'prefix' => 'transactions'
            ], function () {

                /**Mẫu */
                Route::get('', [TransitionStatisticsController::class, 'transactionsStatistical']);
                // Route::get('room-using-total', [TransitionStatisticsController::class, 'getRoomUsingTotalByDate']);
                Route::get('by-date', [TransitionStatisticsController::class, 'getTransactionsByDate']);
                Route::get('export-transactions', [TransitionStatisticsController::class, 'exportExcelStatistical']);
            });
            /** Thống kê nhân viên */
            Route::group([
                'prefix' => 'employees'
            ], function () {
                Route::get('', [EmployeeStatisticsController::class, 'employeesStatistical']);
                Route::get('by-date', [EmployeeStatisticsController::class, 'getEmployeesByDate']);
                Route::get('export-transactions', [EmployeeStatisticsController::class, 'exportExcelStatistical']);
            });
            /** Thống kê loại phòng */
            Route::group([
                'prefix' => 'roomtype'
            ], function () {
                Route::get('', [RoomtypeStatisticsController::class, 'roomtypeStatistical']);
                Route::get('total-roomtype', [RoomtypeStatisticsController::class, 'getTotalRoomsByHotel']);
                Route::get('export-roomtype', [RoomtypeStatisticsController::class, 'exportRoomTypeData']);
            });
        });

        Route::group([
            'prefix' => 'home-hotel'
        ], function () {
            Route::get('get-room-using', [HomeHotelController::class, 'getRoomUsing']);
            Route::get('get-room-using-guest', [HomeHotelController::class, 'getRoomUsingGuest']);
            Route::post('add-guest-room-using', [HomeHotelController::class, 'addGuestRoomUsing']);
        });

        // Phương thức thanh toán
        Route::group([
            'prefix' => 'payment-method'
        ], function () {
            Route::get('get-list', [PaymentMethodController::class, 'index']);
            Route::get('options', [PaymentMethodController::class, 'getCombobox']);
            Route::post('', [PaymentMethodController::class, 'store']);
            Route::get('', [PaymentMethodController::class, 'show']);
            Route::put('{uuid}', [PaymentMethodController::class, 'update']);
            Route::delete('{uuid}', [PaymentMethodController::class, 'destroy']);
        });

        // Thanh toán MoMo
        Route::group([
            'prefix' => 'payment-momo'
        ], function () {
            // MoMo sẽ redirect về đây sau khi người dùng hoàn thành thanh toán
            // Gửi yêu cầu thanh toán tới MoMo
            Route::post('', [PaymentController::class, 'createPayment']);
            Route::get('return', [PaymentController::class, 'handleReturn']); // Xử lý phản hồi từ MoMo
            // MoMo sẽ gửi thông báo trạng thái thanh toán qua webhook
            Route::post('notify', [PaymentController::class, 'handleNotify']); // Xử lý webhook notify từ MoMo

        });
    });
});
