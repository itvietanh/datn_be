<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\EmployeeRequest;

// Service
use App\Services\Api\EmployeeService;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{

    protected $service;

    public function __construct(EmployeeService $service)
    {
        $this->service = $service;
    }

    public function store(EmployeeRequest $request)
    {
        $dataReq = $request->all();
        $dataReq['password'] = Hash::make($dataReq['password']);
        $data = $this->service->create($dataReq);
        return $this->responseSuccess($data, 201);
    }

    public function login(Request $request)
    {
        // Lấy thông tin email và password từ request
        $credentials = (object) $request->only('email', 'password');

        $employee = $this->service->findByEmail($credentials->email);

        // Kiểm tra xem nhân viên có tồn tại và mật khẩu có khớp không
        if ($employee && Hash::check($credentials->password, $employee->password)) {
            // Tạo token cho nhân viên
            $token = $employee->createToken('Personal Access Token')->accessToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'employee' => $employee
            ]);
        }

        return response()->json(['error' => 'Thông tin đăng nhập không đúng'], 401);
    }

    public function checkLogin(Request $request)
    {
        if (Auth::guard('employee')->check()) {
            return response()->json(['message' => 'Đã đăng nhập', 'employee' => Auth::guard('employee')->user()]);
        }

        return response()->json(['message' => 'Chưa đăng nhập'], 401);
    }
}
