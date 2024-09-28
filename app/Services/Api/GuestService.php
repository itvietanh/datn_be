<?php

namespace App\Services\Api;

use App\Services\BaseService;


use App\Models\Guest;

class GuestService extends BaseService
{
    // Service logic here
    public function __construct()
    {
        $this->model = new Guest();
    }
}
