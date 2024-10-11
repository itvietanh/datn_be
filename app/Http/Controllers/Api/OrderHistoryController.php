<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\OrderHistoryService;

class OrderHistoryController extends BaseController
{
    protected $service;

    public function __construct(OrderHistoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $req) {
        $data = $this->service->getOrderHistory($req);
        return $this->getPaging($data);
    }
}
