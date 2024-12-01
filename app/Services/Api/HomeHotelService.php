<?php

namespace App\Services\Api;

use App\Services\BaseService;

//Models
use App\Models\Guest;
use App\Models\RoomType;
use App\Models\Transition;
use App\Models\RoomUsing;
use App\Models\RoomUsingGuest;
use App\Models\RoomUsingService;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeHotelService extends BaseService
{
    public function __construct() {}

    public function handleGetRoomUsing(Request $req)
    {
        $query = DB::table('room as r')
            ->select(
                'r.uuid as roomUuid',
                'RUG.uuid as rugUuid',
                'RU.uuid as ruUuid',
                'RUG.check_in as checkIn',
                'RUG.check_out as checkOut',
                'G.name',
                'G.phone_number AS phoneNumber',
                'G.id_number as idNumber',
                'G.passport_number as passportNumber',
                'G.contact_details as addressDetail',
                'G.representative',
                'G.birth_date AS birthDate',
                'G.gender',
                'G.nat_id as natId',
                'r.room_number as roomNumber',
                'RT.type_name as typeName',
                'RT.number_of_people as numberOfPeople',
                'S.service_name AS serviceName',
                'T.uuid as transUuid',

            )
            ->join('hotel as h', 'r.hotel_id', '=', 'h.id')
            ->join('room_type as RT', 'RT.id', '=', 'r.room_type_id')
            ->join('room_using as RU', 'r.id', '=', 'RU.room_id')
            ->leftJoin('room_using_guest as RUG', 'RU.id', '=', 'RUG.room_using_id')
            ->leftJoin('guest as G', 'RUG.guest_id', '=', 'G.id')
            ->leftJoin('room_using_service as RUS', 'RU.id', '=', 'RUS.room_using_id')
            ->leftJoin('service as S', 'S.id', '=', 'RUS.service_id')
            ->leftJoin('transition as T', 'T.id', '=', 'RU.trans_id');

        if ($req->has('uuid')) {
            $query->where('r.uuid', $req->uuid);
        }
        $query->where('G.representative', true);
        $data = $this->getOneQueryBuilder($query);
        return $data;
    }

    public function handleGetRoomUsingGuest($req)
    {
        $query = DB::table('room_using_guest as rug')
            ->select(
                'g.name',
                'g.uuid as guestUuid',
                'g.contact_details as addressDetail',
                'g.id_number as idNumber',
                'g.passport_number as passportNumber',
                'g.province_id as provinceId',
                'g.district_id as districtId',
                'g.ward_id as wardId',
                'g.phone_number as phoneNumber',
                'g.representative',
                'g.gender',
                'g.nat_id as natId',
                'g.contact_details as contactDetails',
                'g.birth_date as birthDate',
                'rug.check_in as checkIn',
                'rug.check_out as checkOut',
            )
            ->join('guest as g', 'rug.guest_id', '=', 'g.id')
            ->join('room_using as ru', 'rug.room_using_id', '=', 'ru.id')
            ->join('transition as t', 't.id', '=', 'ru.trans_id');
        $query->where('t.uuid', $req->uuid);
        return $this->getListQueryBuilder($req, $query);
    }

    public function handleAddGuestInRoomUsing($req)
    {
        $guestId = [];
        if (!empty($req->guests)) {
            $guest = $req->guests;
            foreach ($guest as $item) {
                $this->model = new Guest();
                $guestId[] = $this->create($item);
            }
        }

        // dd($guestId);

        if (!empty($req->roomUsingGuest)) {
            $checkIn = $this->convertLongToTimestamp($req->roomUsingGuest['check_in']);
            $checkOut = $this->convertLongToTimestamp($req->roomUsingGuest['check_out']);
            $roomUsingGuest = $req->roomUsingGuest;
            $roomUsing = $this->getRoomUsingByUuid($req->roomUsingGuest['ruUuid']);
            
            foreach ($guestId as $value) {
                $roomUsingGuest['guest_id'] = $value->id;
                $roomUsingGuest['room_using_id'] = $roomUsing->id;
                $roomUsingGuest['check_in'] = $checkIn;
                $roomUsingGuest['check_out'] = $checkOut;
                $this->model = new RoomUsingGuest();
                $data = $this->create($roomUsingGuest);
                return $data;
            }
        }
    }

    public function getRoomUsingByUuid($uuid)
    {
        $this->model = new RoomUsing();
        return $this->findFirstByUuid($uuid);
    }
}
