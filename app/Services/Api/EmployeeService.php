<?php

namespace App\Services\Api;

use App\Services\BaseService;

use App\Models\Employee;

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
        return $query->where('email', $email)->first();
    }
}
