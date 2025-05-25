<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            UserProfileSeeder::class, 
            CategorySeeder::class, 
            ItemSeeder::class,
            CategoryItemSeeder::class, 
            LikeSeeder::class,
            CommentSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
