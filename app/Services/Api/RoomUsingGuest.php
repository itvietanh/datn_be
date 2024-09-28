<?php

 namespace App\Services\Api;

   use App\Models\RoomUsingGuest as RoomUsingGuestModel;
   use App\Services\BaseService;

    class RoomUsingGuest extends BaseService
    {
        // Service logic here
        public function __construct()
        {
           
            $this->model = new RoomUsingGuestModel();
        }
    }
