<?php

namespace App\Services\Api;

use App\Models\Shift;
use App\Services\BaseService;

class ShiftService extends BaseService
{
    public function __construct()
    {
        $this->model = new Shift();
    }
    public function findFirstById($id)
    {
        return Shift::where('id', $id)->first();
    }
    // public function update($id, $data)
    // {
    //     $shift = Shift::findOrFail($id);
    //     $shift->update($data);
    //     return $shift;
    // }
}
