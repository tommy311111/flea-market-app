<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Comment;

class CommentsSeeder extends Seeder
{
    public function run(): void
    {
        $commentData = [
            '佐藤 美咲' => 5,
            '鈴木 大輔' => 5,
            '高橋 結衣' => 0,
        ];

        foreach ($commentData as $userName => $commentCount) {
            $user = User::firstWhere('name', $userName);

            if ($user && $commentCount > 0) {
                Comment::factory()
                    ->count($commentCount)
                    ->for($user)
                    ->create();
            }
        }
    }
}
