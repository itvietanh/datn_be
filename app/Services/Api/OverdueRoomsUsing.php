<?php

namespace App\Services\Api;

use App\Services\BaseService;

use App\Models\RoomUsing as RoomUsing;

class OverdueRoomsUsing extends BaseService
{
    // Service logic here
    public function __construct()
    {
        $this->model = new RoomUsing();
    }
}
