<?php

namespace App\Services\Api;

use App\Models\PaymentMethod;
use App\Services\BaseService;

class PaymentMethodService extends BaseService
{
    public function __construct()
    {
        $this->model = new PaymentMethod();
    }
}
