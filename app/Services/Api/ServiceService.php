<?php

    namespace App\Services\Api;

use App\Models\Service;
use App\Services\BaseService;

    class ServiceService extends BaseService
    {
        // Service logic here
        public function __construct()
        {
            $this->model = new Service();
        }
    }