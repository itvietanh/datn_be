<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\ServiceCategories;
use App\Services\Api\ServiceService;
use Illuminate\Http\Request;

class ServiceCatgoriesController extends BaseController
{
    protected $service;

    public function __construct(ServiceService $service)
    {
        $this->service = $service;
    }

    public function getData(Request $req)
    {
        $data = $this->service->getCombobox($req);

        return $this->getPaging($data);
    }
}
