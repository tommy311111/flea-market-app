<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;

class LikeFactory extends Factory
{
    protected $model = \App\Models\Like::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            // 論理削除のdeleted_atがあるならnullにしておく
            'deleted_at' => null,
        ];
    }
}
