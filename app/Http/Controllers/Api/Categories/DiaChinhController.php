<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Services\Api\DiaChinhService;

class DiaChinhController extends BaseController
{

    protected $service;

    public function __construct(DiaChinhService $service)
    {
        $this->service = $service;
    }

    public function index(Request $req) {}

    public function getCombobox(Request $req)
    {
        $capDiaChinh = $req->capDiaChinh;
        $wardCode = $req->village;

        $data = [];

        if ($capDiaChinh === "T") {
            $data = $this->service->getProvinces($req);
        }

        if ($capDiaChinh === "Q") {
            $data = $this->service->getDistrictsByProvince($req);
        }

        if ($capDiaChinh === "P") {
            $data = $this->service->getWardsByDistrict($req);
        }

        return $this->getPaging($data);
    }

    public function store() {}


    public function show(Request $req)
    {
        //

    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
