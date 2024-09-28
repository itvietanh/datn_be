<?php

namespace App\Services\Api;

use App\Services\BaseService;
use App\Models\Floor;

class FloorService extends BaseService
{
    // Service logic here
    public function __construct()
    {
        $this->model = new Floor();
    }
}
