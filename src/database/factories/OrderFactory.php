<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'payment_method' => 'カード支払い',
            'sending_postcode' => '123-4567',
            'sending_address' => '東京都千代田区',
            'sending_building' => 'テストビル',
        ];
    }
}