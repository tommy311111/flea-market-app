<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['nullable', 'image', 'mimes:jpeg,png'],
            'name' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.image' => '「.png」または「.jpeg」形式でアップロードしてください。',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
            'name.required' => 'ユーザー名を入力してください',
            'name.string' => 'ユーザー名は文字列で入力してください',
            'name.max' => 'ユーザー名は255文字以内で入力してください',
            'postcode.required' => '郵便番号を入力してください',
            'postcode.regex' => '郵便番号はハイフン（-）を含めた8文字で入力してください（例：123-4567）',
            'address.required' => '住所を入力してください',
            'building.required' => '建物名を入力してください',
        ];
    }
}
