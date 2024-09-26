<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Model
use App\Models\Transition;
use App\Models\Guest;

class TransitionController extends BaseController
{
    /**
     * Lấy danh sách tất cả các transitions
     */
    public function index(Request $req)
    {
        $fillable = ['uuid', 'guest_id', 'transition_date', 'payment_status', 'created_at', 'updated_at', 'created_by', 'updated_by'];

        $data = Transition::select($fillable)->paginate($req->input('size', 10));

        return $this->getPaging($data);
    }

    /**
     * Tạo một transition mới
     */
    public function store(Request $req)
    {
        $validated = $req->validate([
            'guest_id' => 'required|exists:guest,id',
            'transition_date' => 'required|date',
            'payment_status' => 'required|integer',
        ]);

        $params = $req->all();
        $transition = Transition::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'guest_id' => $validated['guest_id'],
            'transition_date' => $validated['transition_date'],
            'payment_status' => $validated['payment_status']
        ]);

        return $this->responseSuccess($transition, 201);
    }

    /**
     * Hiển thị một transition
     */
    public function show(Request $req, $uuid)
    {
        $transition = Transition::where('uuid', $uuid)->firstOrFail();
        return $this->oneResponse($transition);
    }

    /**
     * Cập nhật một transition
     */
    public function update(Request $req, $uuid)
    {
        $validated = $req->validate([
            'guest_id' => 'required|exists:guest,id',
            'transition_date' => 'required|date',
            'payment_status' => 'required|boolean',
        ]);

        $transition = Transition::where('uuid', $uuid)->firstOrFail();

        $transition->update([
            'guest_id' => $validated['guest_id'],
            'transition_date' => $validated['transition_date'],
            'payment_status' => $validated['payment_status'],
            'updated_at' => now(),
        ]);

        return $this->responseSuccess($transition);
    }

    /**
     * Xóa một transition
     */
    public function destroy($uuid)
    {
        $transition = Transition::where('uuid', $uuid)->firstOrFail();

        $transition->delete();

        return $this->responseSuccess([
            'message' => 'Transition deleted successfully'
        ]);
    }
}
