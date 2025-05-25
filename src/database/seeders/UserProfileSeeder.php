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

        foreach ($users as $index => $user) {
            $profile = UserProfile::factory()->make();

            // 最初の2人だけ画像をつける
            if ($index === 0) {
                $profile->image = 'images/profiles/cat.jpeg';
            } elseif ($index === 1) {
                $profile->image = 'images/profiles/dog.jpeg';
            }

            $profile->user_id = $user->id;
            $profile->save();
        }
    }
}
