<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function likes()
    {
    return $this->hasMany(Like::class)->whereNull('deleted_at');
    }

    public function isLikedBy($user)
{
    return $this->likes()->where('user_id', $user->id)->exists();
}

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function comments()
    {
    return $this->hasMany(Comment::class);
    }

    public const CONDITIONS = ['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'];
}
