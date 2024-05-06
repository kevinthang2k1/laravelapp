<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the customer is authorized to make this request.
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
            'email' => 'required|string|email|unique:customers|max:191',
            'name' => 'required|string',
            'customer_catalogue_id' => 'required|integer|gt:0',
            'password' => 'required|string|min:6',
            're_password' => 'required|string|same:password'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Nhập email vào bạn ơi.',
            'email.email' => 'Email chưa đúng bạn ơi.Ví dụ:abc@gmail.com',
            'email.uique' => 'Email đã tồn tại . Chọn Email khác đi bạn ơi.',
            'email.string' => 'Email phải dạng ký tự',
            'email.max' => 'Độ dài email tối đa 191 ký tự ',
            'name.required' => 'Mày chưa nhập họ và tên ',
            'name.string' => 'Họ tên mày phải là dạng ký tự',
            'customer_catalogue_id.gt' => 'Bạn chưa chọn nhóm thành viên',
            'password.required' => 'Nhập password vào bạn ơi.',
            're_password.required' => 'Bạn phải nhập vào ô nhập lại mật khẩu',
            're_password.same' => 'Mật khẩu không khớp',
        ];
    }
}
