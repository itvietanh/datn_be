<?php

namespace App\Services\Api;

use App\Services\BaseService;

use App\Models\Provinces;
use App\Models\Districts;
use App\Models\DmQuocTich;
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
        $searchParams = (object) $req->only(['q', 'values', 'tenNganGon']);
        $this->model = new Provinces();
        $data = $this->getList($req, $coulumn, function ($query) use ($searchParams) {
            if (!empty($searchParams->q)) {
                $q = mb_strtolower(trim($searchParams->q), 'UTF-8');
                $query->whereRaw('LOWER(full_name) LIKE ?', ['%' . $q . '%']);
            }

            if (!empty($searchParams->tenNganGon)) {
                $tenNganGon = mb_strtolower(trim($searchParams->tenNganGon), 'UTF-8');
                $query->whereRaw('LOWER(full_name) LIKE ?', ['%' . $tenNganGon . '%']);
            }

            if (!empty($searchParams->values)) {
                $query->where('code', '=', $searchParams->values);
            }
        }, true);
        return $data;
    }

    public function getDistrictsByProvince(Request $req)
    {
        $coulumn = ['code as value', 'name', 'full_name as label'];
        $searchParams = (object) $req->only(['diaChinhChaId', 'values', 'q', 'tenNganGon']);

        $this->model = new Districts();
        $data = $this->getList($req, $coulumn, function ($query) use ($searchParams) {
            if (!empty($searchParams->values)) {
                $query->where('code', '=', $searchParams->values);
            }

            if (!empty($searchParams->tenNganGon)) {
                $tenNganGon = mb_strtolower(trim($searchParams->tenNganGon), 'UTF-8');
                $query->whereRaw('LOWER(full_name) LIKE ?', ['%' . $tenNganGon . '%']);
            }

            if (!empty($searchParams->diaChinhChaId)) {
                $query->where('province_code', '=', $searchParams->diaChinhChaId);
            }

            if (!empty($searchParams->q)) {
                $q = mb_strtolower(trim($searchParams->q), 'UTF-8');
                $query->whereRaw('LOWER(full_name) LIKE ?', ['%' . $q . '%']);
            }
        }, true);
        return $data;
    }

    public function getWardsByDistrict(Request $req)
    {
        $coulumn = ['code as value', 'name', 'full_name as label'];
        $searchParams = (object) $req->only(['diaChinhChaId', 'values', 'q', 'tenNganGon']);
        $this->model = new Wards();
        $data = $this->getList($req, $coulumn, function ($query) use ($searchParams) {
            if (!empty($searchParams->values)) {
                $query->where('code', '=', $searchParams->values);
            }

            if (!empty($searchParams->tenNganGon)) {
                $tenNganGon = mb_strtolower(trim($searchParams->tenNganGon), 'UTF-8');
                $query->whereRaw('LOWER(full_name) LIKE ?', ['%' . $tenNganGon . '%']);
            }

            if (!empty($searchParams->diaChinhChaId)) {
                $query->where('district_code', '=', $searchParams->diaChinhChaId);
            }

            if (!empty($searchParams->q)) {
                $q = mb_strtolower(trim($searchParams->q), 'UTF-8');
                $query->whereRaw('LOWER(full_name) LIKE ?', ['%' . $q . '%']);
            }
        }, true);
        return $data;
    }

    public function getDmQuocTich($req)
    {
        $this->model = new DmQuocTich();

        $params = (object) $req->only(['values', 'q']);

        $columns = ['id as value', 'ten as label'];
        $data = $this->getList($req, $columns, function ($query) use ($params) {
            if (!empty($params->values)) {
                $query->where('id', '=', $params->values);
            }

            if (!empty($params->q)) {
                $query->where('ten', 'like', '%' . $params->q . '%');
            }
        });

        return $data;
    }
}
