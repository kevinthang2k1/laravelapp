<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Nhập email vào bạn ơi.',
            'email.email' => 'Email chưa đúng bạn ơi.Ví dụ:abc@gmail.com',
            'password.required' => 'Nhập password vào bạn ơi.',
            'password.password' => 'Nhập password vào bạn ơi.',

        ];
    }
}
