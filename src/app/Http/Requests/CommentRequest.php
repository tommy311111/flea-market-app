<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'コメントを入力してください',
            'body.max' => '255文字以内で入力してください',
        ];
    }
}
