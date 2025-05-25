<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;

class CommentFactory extends Factory
{
    protected static array $commentSamples = [
        'Great condition, thanks!',
        'Fast shipping. Much appreciated.',
        'Looks even better in person!',
        'Carefully packed and arrived safely.',
        'Would buy from again!',
        'Item smaller than expected.',
        'Slight scratch but still usable.',
        'Very smooth transaction.',
        'Super cute, love it!',
        'Basically like new!',
        'Thanks for accepting the offer!',
        'A little smell, but it’s okay.',
        'Not exactly as pictured.',
        'So happy I found this!',
        'Packaging could be better.'
    ];

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'item_id' => Item::inRandomOrder()->first()->id,
            'body' => static::$commentSamples[array_rand(static::$commentSamples)],
        ];
    }
}
