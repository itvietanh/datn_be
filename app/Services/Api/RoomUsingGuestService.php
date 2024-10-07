<?php

namespace App\Services\Api;

use App\Models\RoomUsingGuest; 
use App\Services\BaseService;

class RoomUsingGuestService extends BaseService
{
    public function __construct()
    {
        $this->model = new RoomUsingGuest(); 
    }
}
