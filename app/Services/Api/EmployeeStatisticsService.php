<?php

namespace App\Services\Api;

use App\Models\Employee;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeeStatisticsService extends BaseService
{
    public function __construct()
    {
        $this->model = new Employee();
    }

    // Lấy tất cả thống kê nhân viên
    public function getAllStatistics()
    {
        $totalEmployees = $this->getTotalEmployees();
        $newEmployeesThisMonth = $this->getNewEmployeesThisMonth();
        $activeEmployees = $this->getActiveEmployees();
        $inactiveEmployees = $this->getInactiveEmployees();

        return [
            'total_employees' => $totalEmployees,
            'new_employees_this_month' => $newEmployeesThisMonth,
            'active_employees' => $activeEmployees,
            'inactive_employees' => $inactiveEmployees,
        ];
    }

    // Tổng số nhân viên
    public function getTotalEmployees()
    {
        return $this->model->count();
    }

    // Nhân viên mới trong tháng hiện tại
    public function getNewEmployeesThisMonth()
    {
        return $this->model->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
    }

    // Nhân viên đang hoạt động (giả sử trạng thái hoạt động dựa trên trường `status = 1`)
    public function getActiveEmployees()
    {
        return $this->model->where('status', 1)->count();
    }

    // Nhân viên không hoạt động (giả sử trạng thái không hoạt động là `status = 0`)
    public function getInactiveEmployees()
    {
        return $this->model->where('payment_status', 0)->count();
    }

    // Lấy nhân viên theo phòng ban (nếu có)
    public function getEmployeesByDepartment($departmentId)
    {
        return $this->model->where('department_id', $departmentId)->get();
    }

    // Lấy danh sách nhân viên theo ngày gia nhập
    public function getEmployeesByDate($date)
    {
        return $this->model->whereDate('created_at', $date)->get();
    }

    // Thống kê theo khoảng thời gian
    public function renderDataStatisticalEmployees($req)
    {
        $totalEmployees = $this->getTotalEmployees();
        $employees = $this->getEmployeesByDateRange($req);
        $data = [
            'employees' => $employees,
            'total_employees' => $totalEmployees,
        ];
        return $data;
    }

    // Lấy danh sách nhân viên theo khoảng thời gian
    public function getEmployeesByDateRange($req)
    {
        $params = $req->all();
        $dateFrom = \DateTime::createFromFormat('Ymd', $params['dateFrom'])->format('Y-m-d');
        $dateTo = \DateTime::createFromFormat('Ymd', $params['dateTo'])->format('Y-m-d');

        return $this->model
            ->whereBetween(DB::raw("DATE(created_at)"), [$dateFrom, $dateTo])
            ->select('name', 'email', 'phone', 'created_at', 'status')
            ->get();
    }
}
