<?php

namespace App\Services\Api;

use App\Models\Service;
use App\Models\ServiceCategories;
use App\Services\BaseService;

class ServiceService extends BaseService
{
    // Service logic here
    public function __construct()
    {
        $this->model = new Service();
    }

    public function getListServiceInCat()
    {
        $this->model = new ServiceCategories();
    }

    public function getCombobox($req)
    {
        $fillable = ['id as value', 'name as label'];

        $searchParams = (object) $req->only(['id', 'q']);

        $this->model = new ServiceCategories();

        $data = $this->getList($req, $fillable, function ($query) use ($searchParams) {
            if (!empty($searchParams->q)) {
                $query->where('type_name', 'like', '%' . $searchParams->q . '%');
            }

            if (!empty($searchParams->id)) {
                $query->where('id', '=', $searchParams->id);
            }
        });

        return $data;
    }
}
