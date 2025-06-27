<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $password = Hash::make('password');

        $users = [
            [
                'name' => '佐藤 美咲',
                'email' => 'misaki@example.com',
                'password' => $password,
                'email_verified_at' => now(),
            ],
            [
                'name' => '鈴木 大輔',
                'email' => 'daisuke@example.com',
                'password' => $password,
                'email_verified_at' => now(),
            ],
            [
                'name' => '高橋 結衣',
                'email' => 'yui@example.com',
                'password' => $password,
                'email_verified_at' => now(),
            ],
            [
                'name' => '田中 直人',
                'email' => 'naoto@example.com',
                'password' => $password,
                'email_verified_at' => now(),
            ],
            [
                'name' => '伊藤 紗季',
                'email' => 'saki@example.com',
                'password' => $password,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
