<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required'],
            'description' => ['nullable', 'string', 'max:1000'],
            'priority' => ['required', 'integer', 'in:1,2,3'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,gif', 'max:2048'],
            // 'priority' => ['required', 'digits_between:1,3'],
            //仕様書に「1000文字以内」の記載がなくても、TEXT型は65,535バイトまで入るので、制限なしだと悪意あるユーザーが極端に長い文字列を送れてしまいます。また nullable を明示しないと空送信時にエラーになります。
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'タスクは必須です',
            'title.string' => 'タスクを文字列で入力してください',
            'title.max' => 'タスクを255文字以内で入力してください',
            'description.max' => '説明は1000文字以内で入力してください。',
            'category_id.required' => 'カテゴリーを入力してください',
            'category_id.exists' => '選択されたカテゴリーは存在しません。',
            'priority.required' => '優先度を入力してください',
            'priority.in' => '優先度を１から３の数値で入力してください',
            'image.image' => '選択する画像は画像ファイルでなければなりません',
            'image.mimes' => '画像は、jpeg、png、gifタイプのファイルでなければなりません',
            'image.max' => '画像サイズは、2048KB以下にしてください',
        ];
    }
}
