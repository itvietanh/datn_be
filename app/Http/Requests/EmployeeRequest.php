<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required',
            'hotel_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên nhân viên không được để trống',
            'name.string' => 'Tên nhân viên không hợp lệ',
            'phone.required' => 'Số điện thoại không được để trống',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'address.required' => 'Địa chỉ không được để trống',
            'hotel_id.required' => 'Không được để trống',
        ];
    }
}
