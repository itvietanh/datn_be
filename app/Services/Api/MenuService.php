<?php

namespace App\Services\Api;

use App\Models\Menu;

use App\Services\BaseService;

class MenuService extends BaseService
{
    public function __construct()
    {
        $this->model = new Menu();
    }

    public function findFirstById($id)
    {
        return Menu::where('id', $id)->first();
    }
}
