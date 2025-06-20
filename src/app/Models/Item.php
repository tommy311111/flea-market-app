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

    public static function search($keyword = null, $page = 'recommend', $user = null)
{
    $query = self::query();

    // 部分一致検索
    if (!empty($keyword)) {
        $query->where('name', 'like', '%' . $keyword . '%');
    }

    // マイリスト：ログインしていなければ空コレクションを返す
    if ($page === 'mylist') {
        if ($user) {
            $query->whereHas('likes', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } else {
            return collect([]); // ログインしていなければマイリストは空
        }
    }

    // おすすめ：ログインしていれば自分の商品を除外、未ログインならそのまま
    if ($page === 'recommend' && $user) {
        $query->where('user_id', '!=', $user->id);
    }

    return $query->latest()->get();
}

}
