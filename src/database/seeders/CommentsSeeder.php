<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;

class CommentsSeeder extends Seeder
{
    public function run(): void
    {
        $commentData = [
            '佐藤 美咲' => 1,
            '鈴木 大輔' => 1,
            '高橋 結衣' => 7,
            '田中 直人' => 5,
            '伊藤 紗季' => 4,
        ];

        $items = Item::pluck('id');

        collect($commentData)->each(function ($count, $name) use ($items) {
            $user = User::where('name', $name)->first();
            if (!$user || $count <= 0) return;

            $items->shuffle()->take($count)->each(function ($itemId) use ($user) {
                Comment::factory()->create([
                    'user_id' => $user->id,
                    'item_id' => $itemId,
                ]);
            });
        });
    }
}
