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
        $buyer1 = User::where('name', '田中 直人')->with('profile')->first();
        $item1 = Item::where('name', 'ショルダーバッグ')->first();

        if ($buyer1 && $item1 && $buyer1->profile) {
            Order::create([
                'user_id' => $buyer1->id,
                'item_id' => $item1->id,
                'payment_method' => 'コンビニ払い',
                'sending_postcode' => $buyer1->profile->postcode,
                'sending_address' => $buyer1->profile->address,
                'sending_building' => $buyer1->profile->building ?? '',
            ]);
        }

        $buyer2 = User::where('name', '鈴木 大輔')->with('profile')->first();
        $item2 = Item::where('name', 'タンブラー')->first();

        if ($buyer2 && $item2 && $buyer2->profile) {
            Order::create([
                'user_id' => $buyer2->id,
                'item_id' => $item2->id,
                'payment_method' => 'カード支払い',
                'sending_postcode' => $buyer2->profile->postcode,
                'sending_address' => $buyer2->profile->address,
                'sending_building' => $buyer2->profile->building ?? '',
            ]);
        }

        $buyer3 = User::where('name', '田中 直人')->first();
        $item3 = Item::where('name', 'コーヒーミル')->first();

        if ($buyer3 && $item3) {
            Order::create([
                'user_id' => $buyer3->id,
                'item_id' => $item3->id,
                'payment_method' => 'カード支払い',
                'sending_postcode' => '131-0045',
                'sending_address' => '東京都墨田区押上1-1-2',
                'sending_building' => '東京スカイツリー',
            ]);
        }
    }
}
