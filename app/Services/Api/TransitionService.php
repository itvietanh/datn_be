<?php

namespace App\Services\Api;

use App\Models\RoomUsing;
use App\Models\Transition;

use App\Services\BaseService;

class TransitionService extends BaseService
{
    public function __construct()
    {
        $this->model = new Transition();
    }
   
}
