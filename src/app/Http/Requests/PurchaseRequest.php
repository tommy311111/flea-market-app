<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required'],
            'sending_postcode' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'],
            'sending_address' => ['required', 'string', 'max:255'],
            'sending_building' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'sending_postcode.required' => '配送先を選択してください',
            'sending_address.required' => '配送先を選択してください',
            'sending_building.required' => '配送先を選択してください',
        ];
    }
}
