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
            '佐藤 美咲' => 5,
            '鈴木 大輔' => 5,
            '高橋 結衣' => 0,
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
