<?php

namespace App\Services\Api;

use App\Services\BaseService;

use App\Models\RoomUsingService as RoomUsingServiceModel;

class RoomUsingService extends BaseService
{
    // Service logic here
    public function __construct()
    {
        // Cái này nó khởi tạo model cho biến model bên trong BaseService; thì controller sẽ chọc vảo service rồi mới vào controller. Không gọi trực tiếp vào model luôn như đi học nữa nha
        $this->model = new RoomUsingServiceModel();
    }
}
