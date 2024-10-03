<?php

namespace App\Services\Api;

use App\Services\BaseService;

use App\Models\RoomUsingService as RoomUsingServiceModel;

class RoomUsingService extends BaseService
{
    // Service logic here
    public function __construct()
    {
        $this->model = new RoomUsingServiceModel();
    }
}
