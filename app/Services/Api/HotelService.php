<?php

namespace App\Services\Api;

use App\Models\Hotel;

use App\Services\BaseService;

class HotelService extends BaseService
{
    public function __construct()
    {
        $this->model = new Hotel();
    }
}
