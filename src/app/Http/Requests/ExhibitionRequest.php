<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required'],
            'description' => ['required', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpeg,png'],
            'category' => ['required', 'array', 'min:1'],
            'category.*' => ['required', 'exists:categories,id'],
            'condition' => ['required', Rule::in(\App\Models\Item::CONDITIONS)],
            'price' => ['required', 'numeric', 'min:0'],
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
