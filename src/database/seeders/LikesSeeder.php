<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;

class LikesSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            '佐藤 美咲' => 0,
            '鈴木 大輔' => 0,
            '高橋 結衣' => 7,
            '田中 直人' => 6,
            '伊藤 紗季' => 4,
        ])->each(function ($likeCount, $userName) {
            $user = User::where('name', $userName)->first();
            if (!$user || $likeCount === 0) return;

            Item::inRandomOrder()
                ->take($likeCount)
                ->pluck('id')
                ->each(function ($itemId) use ($user) {
                    Like::create([
                        'user_id' => $user->id,
                        'item_id' => $itemId,
                    ]);
                });
        });
    }
}
