<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($this->category),
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'カテゴリー名は必須です',
            'name.max' => 'カテゴリー名は255文字以内で入力してください',
            'name.unique' => 'このカテゴリー名はすでに使用されています',
        ];
    }
}
