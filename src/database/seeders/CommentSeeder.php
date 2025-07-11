<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Comment;
use App\Models\Item;

class CommentSeeder extends Seeder
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

        $items = Item::pluck('id')->toArray();

        foreach ($commentData as $userName => $commentCount) {
            $user = User::where('name', $userName)->first();

            if ($user && $commentCount > 0) {
                $shuffledItemIds = collect($items)->shuffle()->take($commentCount);

                foreach ($shuffledItemIds as $itemId) {
                    Comment::factory()->create([
                        'user_id' => $user->id,
                        'item_id' => $itemId,
                    ]);
                }
            }
        }
    }
}
