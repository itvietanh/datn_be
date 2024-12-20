<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Services\Api\FloorService;
use Illuminate\Support\Facades\Log;

class FloorController extends BaseController
{
    protected $service;
    public function __construct(FloorService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = DB::table('floor')
            ->select(
                'floor.id',
                'floor.uuid',
                'floor.hotel_id AS hotelId',
                'floor.floor_number AS floorNumber',
                'floor.created_at AS createdAt',
                'floor.updated_at AS updatedAt',
                DB::raw("COALESCE(
            jsonb_agg(
                DISTINCT jsonb_build_object(
                    'ruUuid', room_using.uuid,
                    'prepaid', room_using.prepaid,
                    'room_change_fee', room_using.room_change_fee,
                    'roomUuid', room.uuid,
                    'roomNumber', room.room_number,
                    'status', room.status,
                    'typeName', room_type.type_name,
                    'numberOfPeople', room_type.number_of_people,
                    'checkIn', room_using.check_in,
                    'checkOut', room_using.check_out,
                    'totalGuests', (
                        SELECT COUNT(*)
                        FROM room_using_guest rug
                        WHERE rug.room_using_id = room_using.id and room.status = 2 and rug.deleted_at is null
                    ),
                    'room_using_guest', (
                        SELECT jsonb_agg(
                            DISTINCT jsonb_build_object(
                                'uuid', guest.uuid,
                                'name', guest.name,
                                'phoneNumber', guest.phone_number,
                                'idNumber', guest.id_number,
                                'representative', guest.representative
                            )
                        )
                        FROM room_using_guest rug
                        JOIN guest ON rug.guest_id = guest.id
                        WHERE (rug.room_using_id = room_using.id AND room_using.deleted_at IS NULL)
                    )
                )
            ) FILTER (WHERE room.id IS NOT NULL),
            '[]'::jsonb
        ) AS rooms")
            )
            ->join('hotel', 'floor.hotel_id', '=', 'hotel.id')
            ->leftJoin('room', 'room.floor_id', '=', 'floor.id')
            ->leftJoin('room_type', 'room.room_type_id', '=', 'room_type.id')
            ->leftJoin('room_using', function ($join) {
                $join->on('room.id', '=', 'room_using.room_id')
                    ->whereNull('room_using.deleted_at');
            })
            ->leftJoin('room_using_guest', 'room_using.id', '=', 'room_using_guest.room_using_id')
            ->leftJoin('guest', 'room_using_guest.guest_id', '=', 'guest.id')
            ->groupBy('floor.id', 'floor.uuid', 'floor.hotel_id', 'floor.floor_number', 'floor.created_at', 'floor.updated_at')
            ->orderBy('floor.floor_number', 'ASC');

        if ($request->has('floor_id')) {
            $query->where('floor.id', $request->floor_id);
        }

        if ($request->has('status')) {
            $query->where('room.status', $request->status);
        }

        $data = $this->service->getListQueryBuilder($request, $query);

        // Chuyển đổi rooms từ chuỗi JSON sang đối tượng JSON
        $data->getCollection()->transform(function ($item) {
            $item->rooms = json_decode($item->rooms);
            return $item;
        });

        return $this->getPaging($data);
    }



    public function getCombobox(Request $req)
    {
        $fillable = ['id as value', DB::raw("CONCAT('Tầng ', floor_number) as label")];

        $searchParams = (object) $req->only(['id', 'q']);

        $data = $this->service->getList($req, $fillable, function ($query) use ($searchParams) {
            if (!empty($searchParams->q)) {
                $query->where('name', 'like', '%' . $searchParams->q . '%');
            }

            if (!empty($searchParams->id)) {
                $query->where('id', '=', $searchParams->id);
            }
        });

        return $this->getPaging($data);
    }


    /**
     * Show the form for creating a new resource.
     */
    // public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dataReq = $request->validate([
            'hotel_id' => 'required|integer',
            'floor_number' => 'required|integer'
        ]);

        $floor = $this->service->create($dataReq);

        return $this->responseSuccess($floor, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $req)
    {
        $floor = $this->service->findFirstByUuid($req->uuid);
        if (!$floor) $this->response404();
        return $this->oneResponse($floor);
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(string $id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $req)
    {
        $floor = $this->service->findFirstByUuid($req->uuid);
        if (!$floor) $this->response404();
        $data = $this->service->update($floor->id, $req->all());
        return $this->responseSuccess($data);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $req)
    {
        $floor = $this->service->findFirstByUuid($req->uuid);
        if (!$floor) {
            return $this->response404();
        }
        if ($floor->rooms()->exists()) {
            Log::warning('Không thể xóa tầng vì có phòng liên kết', ['floor_uuid' => $req->uuid]);

            return response()->json([
                'message' => 'Không thể xóa tầng vì còn phòng liên kết'
            ], 400);
        }
        $this->service->delete($floor->id);
        return $this->responseSuccess($floor);
    }
}
