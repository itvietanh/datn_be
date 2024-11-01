<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\Api\EmployeeStatisticsService;
use Illuminate\Http\Request;

class EmployeeStatisticsController extends BaseController
{
    protected $employeeStatisticsService;

    public function __construct(EmployeeStatisticsService $employeeStatisticsService)
    {
        $this->employeeStatisticsService = $employeeStatisticsService;
    }

    // trả về tổng số nhân viên
    public function totalEmployees()
    {
        $totalEmployees = $this->employeeStatisticsService->getTotalEmployees();

        // Kiểm tra nếu không có nhân viên nào
        if ($totalEmployees === 0) {
            return $this->responseNotFound('No employees found.');
        }

        return $this->responseSuccess(['total_employees' => $totalEmployees]);
    }

    // trả về số lượng nhân viên mới được thêm vào trong tháng hiện tại
    public function newEmployeesThisMonth()
    {
        $newEmployees = $this->employeeStatisticsService->getNewEmployeesThisMonth();

        // Kiểm tra nếu không có nhân viên mới nào
        if ($newEmployees === 0) {
            return $this->responseNotFound('No new employees found this month.');
        }

        return $this->responseSuccess(['new_employees_this_month' => $newEmployees]);
    }

    //trả về số lượng nhân viên đang hoạt động
    public function activeEmployees()
    {
        $activeEmployees = $this->employeeStatisticsService->getActiveEmployees();

        // Kiểm tra nếu không có nhân viên hoạt động nào
        if ($activeEmployees === 0) {
            return $this->responseNotFound('No active employees found.');
        }

        return $this->responseSuccess(['active_employees' => $activeEmployees]);
    }

    // trả về số lượng nhân viên theo ID khách sạn được chỉ định
    public function employeesByHotel($hotelId)
    {
        $employeesCount = $this->employeeStatisticsService->getEmployeesByHotel($hotelId);

        // Kiểm tra nếu không có nhân viên nào cho khách sạn này
        if ($employeesCount === 0) {
            return $this->responseNotFound('No employees found for the specified hotel.');
        }

        return $this->responseSuccess(['employees_count_by_hotel' => $employeesCount]);
    }

    // trả về thông tin chi tiết về nhân viên
    public function employeeDetails()
    {
        $details = $this->employeeStatisticsService->getEmployeeDetails();

        // Kiểm tra nếu không có chi tiết nhân viên nào
        if (empty($details)) {
            return $this->responseNotFound('No employee details found.');
        }

        return $this->responseSuccess($details);
    }

    // trả về tất cả các thống kê liên quan đến nhân viên
    public function allStatistics()
    {
        $statistics = $this->employeeStatisticsService->getAllStatistics();

        // Kiểm tra nếu không có thống kê nào
        if (empty($statistics)) {
            return $this->responseNotFound('No statistics found.');
        }
        
        return $this->responseSuccess($statistics);
    }

    protected function responseNotFound($message)
    {
        return response()->json(['error' => $message], 404);
    }
}
