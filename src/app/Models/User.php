<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
    

    public function items()
    {
    return $this->hasMany(Item::class);
    }

    // Like モデル経由（詳細な情報が必要な場合）
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    // 論理削除されていない Like 経由で、Items を取得
    public function likedItems(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'likes')
                    ->withTimestamps()
                    ->withPivot('deleted_at')
                    ->wherePivotNull('deleted_at');
    }

    public function orders()
    {
    return $this->hasMany(Order::class);
    }

    public function comments()
    {
    return $this->hasMany(Comment::class);
    }

    public function profileIsFilled()
{
    return $this->profile 
        && $this->profile->postcode 
        && $this->profile->address;
}
}
