<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSlideRequest extends FormRequest
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
            'keyword' => 'required|unique:slides,keyword,'.$this->id.'|max:191',
            'slide.image' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập tên của Slide',
            'keyword.required' => 'Bạn chưa nhập từ khóa của Slide',
            'slide.image.required' => 'Bạn chưa chọn hình ảnh nào cho Slide',
            'keyword.uique' => 'Từ khóa đã tồn tại . Chọn từ khóa khác đi bạn ơi.',
        ];
    }
}
