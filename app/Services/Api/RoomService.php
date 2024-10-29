<?php

namespace App\Services\Api;
use App\Services\BaseService;
use App\Models\Room;
class RoomService extends BaseService
{
    // Service logic here
    public function __construct()
    {
        // $this->model = new YourModel;
        $this->model = new Room();
    }
}



