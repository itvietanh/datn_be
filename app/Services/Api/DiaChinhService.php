<?php

namespace App\Services\Api;

use App\Services\BaseService;

use App\Models\Provinces;
use App\Models\Districts;
use App\Models\Wards;
use Illuminate\Http\Request;

class DiaChinhService extends BaseService
{
    public function __construct()
    {
        // $this->model = new YourModel;
    }

    public function getProvinces(Request $req)
    {
        $coulumn = ['code as value', 'name', 'full_name as label'];
        $searchParams = (object) $req->only(['q', 'value']);
        $this->model = new Provinces();
        $data = $this->getList($req, $coulumn, function ($query) use ($searchParams) {
            if (!empty($searchParams->q)) {
                $query->where('full_name', 'like', '%' . $searchParams->q . '%');
            }

            if (!empty($searchParams->value)) {
                $query->where('code', '=', $searchParams->value);
            }
        });
        return $data;
    }

    public function getDistrictsByProvince(Request $req)
    {
        $coulumn = ['code as value', 'name', 'full_name as label'];
        $searchParams = (object) $req->only(['diaChinhChaId']);
        $this->model = new Districts();
        $data = $this->getList($req, $coulumn, function ($query) use ($searchParams) {
            if (!empty($searchParams->diaChinhChaId)) {
                $query->where('province_code', '=', $searchParams->diaChinhChaId);
            }

            if (!empty($searchParams->q)) {
                $query->where('full_name', 'like', '%' . $searchParams->q . '%');
            }

            if (!empty($searchParams->value)) {
                $query->where('code', '=', $searchParams->value);
            }
        });
        return $data;
    }

    public function getWardsByDistrict(Request $req)
    {
        $coulumn = ['code as value', 'name', 'full_name as label'];
        $searchParams = (object) $req->only(['diaChinhChaId']);
        $this->model = new Wards();
        $data = $this->getList($req, $coulumn, function ($query) use ($searchParams) {
            if (!empty($searchParams->diaChinhChaId)) {
                $query->where('district_code', '=', $searchParams->diaChinhChaId);
            }

            if (!empty($searchParams->q)) {
                $query->where('full_name', 'like', '%' . $searchParams->q . '%');
            }

            if (!empty($searchParams->value)) {
                $query->where('code', '=', $searchParams->value);
            }
        });
        return $data;
    }
}
