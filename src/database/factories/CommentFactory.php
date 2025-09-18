<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

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

    protected static ?array $users = null;
    protected static ?array $items = null;

    public function definition(): array
    {
        if (is_null(static::$users)) {
            static::$users = User::pluck('id')->toArray();
        }
        if (is_null(static::$items)) {
            static::$items = Item::pluck('id')->toArray();
        }

        return [
            'user_id' => static::$users[array_rand(static::$users)],
            'item_id' => static::$items[array_rand(static::$items)],
            'body' => static::$commentSamples[array_rand(static::$commentSamples)],
        ];
    }
}
