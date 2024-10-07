<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;

// Request
use App\Http\Requests\TransitionRequest;
// Service

use App\Services\Api\TransitionService;
use App\Services\Api\RoomUsingService;
use App\Services\Api\RoomUsingGuestService;
use Illuminate\Support\Facades\DB;

class TransitionController extends BaseController
{
    protected $transitionService;
    protected $roomUsingService;
    protected $roomUsingGuestService;

    public function __construct(
        TransitionService $transitionService,
        RoomUsingService $roomUsingService,
        RoomUsingGuestService $roomUsingGuestService
    ) {
        $this->transitionService = $transitionService;
        $this->roomUsingService = $roomUsingService;
        $this->roomUsingGuestService = $roomUsingGuestService;
    }


    /**
     * Lấy danh sách tất cả các transitions
     */
    public function index(Request $request)
    {
        $columns = [
            't.uuid',
            't.guest_id',
            't.transition_date',
            't.payment_status',
            't.created_at',
            't.updated_at',
            't.created_by',
            't.updated_by',
            'ru.room_id',
            'ru.check_in',
            'ru.check_out',
            'rug.guest_id AS room_guest_id',
            'rug.check_in AS guest_check_in',
            'rug.check_out AS guest_check_out'
        ];

        $searchParams = (object) $request->only(['guest_id', 'transition_date']);

        $data = $this->transitionService->getList($request, $columns, function ($query) use ($searchParams, $columns) {
            $query->from('transition AS t')
                ->join('room_using AS ru', 't.id', '=', 'ru.trans_id')
                ->join('room_using_guest AS rug', 'ru.id', '=', 'rug.room_using_id')
                ->select($columns);

            if (isset($searchParams->guest_id)) {
                $query->where('t.guest_id', '=', $searchParams->guest_id);
            }
            if (isset($searchParams->transition_date)) {
                $query->where('t.transition_date', '=', $searchParams->transition_date);
            }
        });

        return $this->getPaging($data);
    }

    /**
     * Tạo một transition mới
     */
    public function store(Request $req)
    {
        DB::beginTransaction();

        try {
            $params = $req->only(['guest_id', 'transition_date', 'payment_status', 'created_by', 'updated_by']);
            $transition = $this->transitionService->create($params);

            $roomUsingData = [
                'trans_id' => $transition->id,
                'room_id' => $req->input('room_id'),
                'check_in' => $req->input('check_in'),
                'check_out' => $req->input('check_out'),
                'created_by' => $req->input('created_by'),
                'updated_by' => $req->input('updated_by'),
            ];
            $roomUsing = $this->roomUsingService->create($roomUsingData);

            $roomUsingGuestData = [
                'guest_id' => $req->input('guest_id'),
                'room_using_id' => $roomUsing->id,
                'check_in' => $req->input('check_in'),
                'check_out' => $req->input('check_out'),
                'created_by' => $req->input('created_by'),
                'updated_by' => $req->input('updated_by'),
            ];
            $this->roomUsingGuestService->create($roomUsingGuestData);


            DB::commit();

            return $this->responseSuccess($transition, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->response404('Lỗi khi tạo transaction', 500, $e->getMessage());
        }
    }

    /**
     * Hiển thị một transition
     */
    public function show(Request $req)
    {
        $data = $this->transitionService->findFirstByUuid($req->uuid, [
            'roomUsings.guests'
        ]);

        if (!$data) {
            return $this->response404('Transition không tồn tại', 404);
        }

        $details = [
            'uuid' => $data->uuid,
            'guest_id' => $data->guest_id,
            'transition_date' => $data->transition_date,
            'payment_status' => $data->payment_status,
            'created_at' => $data->created_at,
            'updated_at' => $data->updated_at,
            'created_by' => $data->created_by,
            'updated_by' => $data->updated_by,
            'room_usings' => $data->roomUsings->map(function ($roomUsing) {
                return [
                    'room_id' => $roomUsing->room_id,
                    'check_in' => $roomUsing->check_in,
                    'check_out' => $roomUsing->check_out,
                    'guests' => $roomUsing->guests->map(function ($guest) {
                        return [
                            'guest_id' => $guest->guest_id,
                            'check_in' => $guest->check_in,
                            'check_out' => $guest->check_out,
                        ];
                    }),
                ];
            })
        ];

        return $this->oneResponse($details);
    }


    /**
     * Cập nhật một transition
     */
    public function update(TransitionRequest $req)
    {
        DB::beginTransaction();

        try {
            $transition = $this->transitionService->findFirstByUuid($req->uuid);

            if (!$transition) {
                return $this->response404('Transition không tồn tại.');
            }

            $transition->update($req->only(['guest_id', 'transition_date', 'payment_status', 'updated_by']));
            if ($req->has('room_usings')) {
                foreach ($req->input('room_usings') as $roomUsingData) {
                    $roomUsing = $transition->roomUsings()->updateOrCreate(
                        ['id' => $roomUsingData['id'] ?? null],
                        [
                            'room_id' => $roomUsingData['room_id'],
                            'check_in' => $roomUsingData['check_in'],
                            'check_out' => $roomUsingData['check_out'],
                            'updated_by' => $req->input('updated_by')
                        ]
                    );
                    if (isset($roomUsingData['guests'])) {
                        foreach ($roomUsingData['guests'] as $guestData) {
                            $roomUsing->guests()->updateOrCreate(
                                ['id' => $guestData['id'] ?? null],
                                [
                                    'guest_id' => $guestData['guest_id'],
                                    'check_in' => $guestData['check_in'],
                                    'check_out' => $guestData['check_out'],
                                    'updated_by' => $req->input('updated_by')
                                ]
                            );
                        }
                    }
                }
            }
            DB::commit();
            return $this->responseSuccess($transition->load('roomUsings.guests'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->response404('Lỗi khi cập nhật Transition', $e->getMessage());
        }
    }

    /**
     * Xóa một transition
     */
    public function destroy(Request $req)
    {
        $transition = $this->transitionService->findFirstByUuid($req->uuid);
        if (!$transition) {
            return $this->response404();
        }
        $data = $this->transitionService->delete($transition->id);
        return $this->responseSuccess($data);
    }
}
