<?php

namespace App\Services\Api;

use App\Services\BaseService;

use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;

class EmployeeService extends BaseService
{
    // Service logic here
    public function __construct()
    {
        $this->model = new Employee();
    }

    public function findByEmail($email)
    {
        $query = $this->model;
        $employee = $query->where('email', $email)->first();
        return $employee;
    }

    public function getAuthorizal($employee)
    {
        $data = EmployeeRole::where('employee_id', $employee->id)->first();
        $role = Role::where('id', $data->role_id)
        ->select('role_name as role')
        ->first();
        return $role;
    }
}
