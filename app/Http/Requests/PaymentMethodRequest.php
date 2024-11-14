<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentMethodRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có quyền thực hiện request này hay không.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Quy tắc xác thực áp dụng cho request.
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'qr_code' => 'nullable|string',
            'description' => 'nullable|string|max:500',
            // Thêm các quy tắc khác tùy theo yêu cầu
        ];
    }
}
