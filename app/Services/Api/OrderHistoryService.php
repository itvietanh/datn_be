<?php

namespace App\Services\Api;

use App\Services\BaseService;
use App\Models\Transition;
use App\Models\RoomUsing;
use App\Models\RoomUsingGuest;
use App\Models\RoomUsingService;
use Illuminate\Support\Facades\DB;

class OrderHistoryService extends BaseService
{

    public function __construct()
    {
        $this->model = new Transition();
    }

    public function getOrderHistory($req)
    {
        $column = [
            "g.name",
            DB::raw("CONCAT('Phòng số ', r.room_number) as room_number"),
            "rug.check_in",
            "rug.check_out",
            "s.service_name",
            "ru.deleted_at",
            "ru.total_amount",
            "t.payment_status"
        ];

        $searchParams = (object) $req->all();

        $data = $this->getList($req, $column, function ($query) use ($searchParams) {

            $query->join('room_using as ru', 'ru.trans_id', '=', 'transition.id')
                ->join('room as r', 'r.id', '=', 'ru.room_id')
                ->join('room_using_guest as rug', 'rug.room_using_id', '=', 'ru.id')
                ->leftJoin('room_using_service as rus', 'rus.room_using_id', '=', 'ru.id')
                ->join('guest as g', 'g.id', '=', 'rug.guest_id')
                ->leftJoin('service as s', 's.id', '=', 'rus.service_id')
                ->leftJoin('transition as t', 't.id', 'ru.trans_id');

            if (isset($searchParams->guest_id)) {
                $query->where('g.id', '=', $searchParams->guest_id);
            }

            if (isset($searchParams->name)) {
                $name = mb_strtolower(trim($searchParams->name), 'UTF-8');
                $query->whereRaw('LOWER(g.name) LIKE ?', ['%' . $name . '%']);
            }

            if (isset($searchParams->room_number)) {
                $roomNumber = trim($searchParams->room_number);
                $query->whereRaw('r.room_number LIKE ?', ['%' . $roomNumber . '%']);
            }
            // Lọc theo check_in (từ ngày)
            if (isset($searchParams->check_in)) {
                $checkIn = $searchParams->check_in;
                $query->whereDate('rug.check_in', '=', $checkIn);
            }

            // Lọc theo check_out (đến ngày)
            if (isset($searchParams->check_out)) {
                $checkOut = $searchParams->check_out;
                $query->whereDate('rug.check_out', '=', $checkOut);
            }

            // Lọc theo payment_status
            if (isset($searchParams->payment_status)) {
                $query->where('t.payment_status', '=', $searchParams->payment_status);
            }
        });

        return $data;
    }
}
