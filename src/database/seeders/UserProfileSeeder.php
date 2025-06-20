<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Storage;

class UserProfileSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

       // 画像ファイルのパスを配列で用意
       $images = [
        'cat.jpeg',
        'dog.jpeg',
        'flowers.jpeg',
        'rabbit.jpeg',
        'snoopy.png',
    ];

    foreach ($users as $index => $user) {
        $profile = UserProfile::factory()->make();

        // ユーザー数が画像数を超えても大丈夫なように、画像をループで使い回す
        $profile->image = $images[$index % count($images)];

        $profile->user_id = $user->id;
        $profile->save();
    }
    }
}
