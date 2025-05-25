<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // ユーザー名ごとのコメント数
        $commentData = [
            '佐藤 美咲' => 0,
            '鈴木 大輔' => 0,
            '高橋 結衣' => 7,
            '田中 直人' => 5,
            '伊藤 紗季' => 2,
        ];

        foreach ($commentData as $userName => $commentCount) {
            $user = \App\Models\User::where('name', $userName)->first();

            if ($user && $commentCount > 0) {
                Comment::factory($commentCount)->create([
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
