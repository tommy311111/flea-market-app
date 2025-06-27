<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Item;

class CategoryItemSeeder extends Seeder
{
    public function run()
    {
        $categoryItems = [
            '腕時計' => ['メンズ'],
            'HDD' => ['家電'],
            '玉ねぎ3束' => ['キッチン'],
            '革靴' => ['メンズ','ファッション'],
            'ノートPC' => ['家電'],
            'マイク' => ['家電'],
            'ショルダーバッグ' => ['レディース','ファッション'],
            'タンブラー' => ['キッチン'],
            'コーヒーミル' => ['キッチン','インテリア'],
            'メイクセット' => ['レディース','コスメ']
        ];

        foreach ($categoryItems as $itemName => $categoryNames) {
            $item = Item::where('name', $itemName)->first();
            $categoryIds = Category::whereIn('name', $categoryNames)->pluck('id')->toArray();

            if ($item && !empty($categoryIds)) {
                $item->categories()->attach($categoryIds);
            }
        }
    }
}
