<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class addressRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'], 
            'postcode' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'], // 例: 123-4567
            'address'     => ['required', 'string', 'max:255'],
            'building'    => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ユーザー名を入力してください',
            'name.string' => 'ユーザー名は文字列で入力してください',
            'name.max' => 'ユーザー名は255文字以内で入力してください',
            'postcode.required' => '郵便番号を入力してください',
            'postcode.regex'    => '郵便番号はハイフン（-）を含めた8文字で入力してください（例：123-4567）',
            'address.required'     => '住所を入力してください',
            'building.required'    => '建物名を入力してください',
        ];
    }
}
