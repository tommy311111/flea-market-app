<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Item;

class OrdersSeeder extends Seeder
{
    public function run()
    {
        $orders = [
            [
                'buyer' => '田中 直人',
                'item' => 'ショルダーバッグ',
                'payment' => 'コンビニ払い',
                'use_profile' => true
            ],
            [
                'buyer' => '鈴木 大輔',
                'item' => 'タンブラー',
                'payment' => 'カード支払い',
                'use_profile' => true
            ],
            [
                'buyer' => '田中 直人',
                'item' => 'コーヒーミル',
                'payment' => 'カード支払い',
                'use_profile' => false,
                'postcode' => '131-0045',
                'address' => '東京都墨田区押上1-1-2',
                'building' => '東京スカイツリー'
            ],
        ];

        foreach ($orders as $order) {
            $buyer = User::firstWhere('name', $order['buyer']);
            $item = Item::firstWhere('name', $order['item']);

            if ($buyer && $item) {
                Order::create([
                    'user_id' => $buyer->id,
                    'item_id' => $item->id,
                    'payment_method' => $order['payment'],
                    'sending_postcode' => $order['use_profile'] ? optional($buyer->profile)->postcode : $order['postcode'],
                    'sending_address' => $order['use_profile'] ? optional($buyer->profile)->address : $order['address'],
                    'sending_building' => $order['use_profile'] ? optional($buyer->profile)->building : $order['building'] ?? '',
                ]);
            }
        }
    }
}
