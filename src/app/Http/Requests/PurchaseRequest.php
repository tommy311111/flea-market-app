<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
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
            'payment_method' => ['required'], // 支払い方法（例: クレカ、コンビニなど）
            'sending_postcode' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'], // 例: 123-4567
            'sending_address'     => ['required', 'string', 'max:255'],
            'sending_building'    => ['required', 'string', 'max:255'],            
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'shipping_address.required' => '配送先を選択してください',
        ];
    }
}
