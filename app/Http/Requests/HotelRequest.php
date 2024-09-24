<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelRequest extends FormRequest
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
            'name' => 'required',
            'address' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên khách sạn không được để trống',
            'address.required' => 'Địa chỉ không được để trống',
        ];
    }
}
