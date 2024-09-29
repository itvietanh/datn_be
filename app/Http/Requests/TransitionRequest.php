<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'guest_id' => 'required',
            'transition_date' => 'required',
            'payment_status' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'guest_id.required' => 'Id khách hàng không được để trống',
            'transition_date.required' => 'Ngày chuyển không được để trống',
            'payment_status.required' => 'Trạng thái thanh toán không được để trống',
        ];
    }
}
