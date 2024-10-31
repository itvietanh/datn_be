<?php

namespace App\Services\Api;

use App\Models\Employee;
use Carbon\Carbon;

class EmployeeStatisticsService
{
    public function getTotalEmployees()
    {
        return Employee::count();
    }

    public function getNewEmployeesThisMonth()
    {
        return Employee::whereMonth('created_at', Carbon::now()->month)
                       ->whereYear('created_at', Carbon::now()->year)
                       ->count();
    }

    public function getActiveEmployees()
    {
        // Bỏ qua cột status, có thể trả về tổng số nhân viên
        return Employee::count(); // Hoặc có thể thay đổi theo nhu cầu
    }

    public function getEmployeesByHotel($hotelId)
    {
        return Employee::where('hotel_id', $hotelId)->count();
    }

    public function getEmployeeDetails()
    {
        return Employee::select('uuid', 'name', 'email', 'phone', 'address', 'hotel_id', 'created_at', 'updated_at', 'created_by', 'updated_by')
                       ->get();
    }

    public function getAllStatistics()
    {
        return [
            'total_employees' => $this->getTotalEmployees(),
            'new_employees_this_month' => $this->getNewEmployeesThisMonth(),
            'active_employees' => $this->getActiveEmployees(),
        ];
    }
}
