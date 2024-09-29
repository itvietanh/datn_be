<?php

namespace App\Services\Api;

use App\Services\BaseService;

use App\Models\EmployeeRole;

class EmployeeRoleService extends BaseService
{
    // Service logic here
    public function __construct()
    {
        $this->model = new EmployeeRole();
    }
}
