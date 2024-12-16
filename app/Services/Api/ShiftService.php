<?php

namespace App\Services\Api;

use App\Models\Shift;
use App\Services\BaseService;

class ShiftService extends BaseService
{
    public function __construct()
    {
        $this->model = new Shift();
    }
}
