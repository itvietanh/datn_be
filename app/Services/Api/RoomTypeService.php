<?php

namespace App\Services\Api;

use App\Services\BaseService;

use App\Models\RoomType;

class RoomTypeService extends BaseService
{
    // Service logic here
    public function __construct()
    {
        $this->model = new RoomType();
    }
}
