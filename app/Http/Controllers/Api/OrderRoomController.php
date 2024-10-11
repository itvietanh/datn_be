<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\Api\OrderRoomService;

use Illuminate\Http\Request;

class OrderRoomController extends BaseController
{
    protected $service;

    public function __construct(OrderRoomService $service)
    {
        $this->service = $service;
    }

    public function store(Request $req) {
        // dd($req);
        $data = $this->service->handleOrderRoom($req);
        return $this->responseSuccess($data, 201);
    }

    public function calulatorPrice(Request $req) {
        $data = $this->service->handleCalculatorPrice($req);
        return $this->responseSuccess($data);
    }
}
