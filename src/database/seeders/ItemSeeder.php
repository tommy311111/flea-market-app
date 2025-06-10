<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;


class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $misaki = User::where('name', '佐藤 美咲')->first();
        $daisuke = User::where('name', '鈴木 大輔')->first();
        $yui = User::where('name', '高橋 結衣')->first();

        $items = [
            [
                'user_id' => $misaki->id,
                'name' => '腕時計',
                'condition' => '良好',
                'price' => 15000,
                'brand_name' => 'ARMANI',
                'image' => 'items/Armani+Mens+Clock.jpg',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
            ],
            [
                'user_id' => $misaki->id,
                'name' => 'HDD',
                'condition' => '目立った傷や汚れなし',
                'price' => 5000,
                'brand_name' => null,
                'image' => 'items/HDD.jpg',
                'description' => '高速で信頼性の高いハードディスク',
            ],
            [
                'user_id' => $misaki->id,
                'name' => '玉ねぎ3束',
                'condition' => 'やや傷や汚れあり',
                'price' => 300,
                'brand_name' => null,
                'image' => 'items/Onion.jpg',
                'description' => '新鮮な玉ねぎ3束のセット',
            ],
            [
                'user_id' => $misaki->id,
                'name' => '革靴',
                'condition' => '状態が悪い',
                'price' => 4000,
                'brand_name' => null,
                'image' => 'items/Shoes.jpg',
                'description' => 'クラシックなデザインの革靴',
            ],
            [
                'user_id' => $misaki->id,
                'name' => 'ノートPC',
                'condition' => '良好',
                'price' => 45000,
                'brand_name' => null,
                'image' => 'items/Laptop.jpg',
                'description' => '高性能なノートパソコン',
            ],
            [
                'user_id' => $misaki->id,
                'name' => 'マイク',
                'condition' => '目立った傷や汚れなし',
                'price' => 8000,
                'brand_name' => null,
                'image' => 'items/Mic.jpg',
                'description' => '高音質のレコーディング用マイク',
            ],
            [
                'user_id' => $daisuke->id,
                'name' => 'ショルダーバッグ',
                'condition' => 'やや傷や汚れあり',
                'price' => 3500,
                'brand_name' => null,
                'image' => 'items/Bag.jpg',
                'description' => 'おしゃれなショルダーバッグ',
            ],
            [
                'user_id' => $yui->id,
                'name' => 'タンブラー',
                'condition' => '状態が悪い',
                'price' => 500,
                'brand_name' => null,
                'image' => 'items/Tumbler.jpg',
                'description' => '使いやすいタンブラー',
            ],
            [
                'user_id' => $yui->id,
                'name' => 'コーヒーミル',
                'condition' => '良好',
                'price' => 4000,
                'brand_name' => null,
                'image' => 'items/Coffee+mill.jpg',
                'description' => '手動のコーヒーミル',
            ],
            [
                'user_id' => $daisuke->id,
                'name' => 'メイクセット',
                'condition' => '目立った傷や汚れなし',
                'price' => 2500,
                'brand_name' => null,
                'image' => 'items/Makeup.jpg',
                'description' => '便利なメイクアップセット',
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
