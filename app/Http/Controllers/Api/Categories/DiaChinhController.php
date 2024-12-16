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

        $data = [];
        
        switch ($capDiaChinh) {
            case 'T':
                $data = $this->service->getProvinces($req);
                break;
            case 'Q':
                $data = $this->service->getDistrictsByProvince($req);
                break;
            case 'P':
                $data = $this->service->getWardsByDistrict($req);
                break;
        }

        return $this->getPaging($data);
    }

    public function getComboboxQT(Request $req)
    {
        $data = $this->service->getDmQuocTich($req);
        return $this->getPaging($data);
    }
}
