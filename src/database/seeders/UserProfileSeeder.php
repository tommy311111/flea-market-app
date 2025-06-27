<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;

class UserProfileSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $images = [
            'cat.jpeg',
            'dog.jpeg',
            'flowers.jpeg',
            'rabbit.jpeg',
            'snoopy.png',
        ];

        foreach ($users as $index => $user) {
            $profile = UserProfile::factory()->make();
            $profile->image = $images[$index % count($images)];
            $profile->user_id = $user->id;
            $profile->save();
        }
    }
}
