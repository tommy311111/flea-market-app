<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = \App\Models\Category::class;

    protected static array $categorySamples = [
        'ファッション', '家電', 'レディース', 'メンズ', 'コスメ', '本', 'ゲーム',
        'スポーツ', 'キッチン', 'ハンドメイド', 'アクセサリー', 'おもちゃ', 'ベビー・キッズ',
    ];

    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(static::$categorySamples),
        ];
    }
}
