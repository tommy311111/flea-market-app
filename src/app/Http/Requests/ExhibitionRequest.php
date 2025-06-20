<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name' => ['required'], // 商品名：必須
            'description' => ['required', 'max:255'], // 説明：必須、255文字まで
            'image' => ['required', 'image', 'mimes:jpeg,png'], // 画像：必須、jpeg/png
            'category' => ['required', 'array','min:1'], // カテゴリー：必須、複数可
            'category.*' => ['required', 'exists:categories,id'], // 各カテゴリー項目：必須
            'condition' => ['required', Rule::in(\App\Models\Item::CONDITIONS)], // 状態：必須
            'price' => ['required', 'numeric', 'min:0'], // 価格：必須、数値、0円以上
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '商品名を入力してください。',
            'description.required' => '商品説明を入力してください。',
            'description.max' => '255文字以内で入力してください。',
            'image.required' => '商品画像をアップロードしてください',
            'image.image' => '画像ファイルを選択してください。',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
            'category.required' => '商品のカテゴリーを選択してください',
            'category.array' => '商品のカテゴリーを選択してください',
            'category.min' => '商品のカテゴリーを選択してください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '商品価格を入力してください',
            'price.numeric' => '数値で入力してください',
            'price.min' => '商品価格は0円以上で入力してください。',
        ];
    }
}
