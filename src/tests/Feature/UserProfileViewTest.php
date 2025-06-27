<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserProfileViewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報がプロフィール画面で取得できる()
    {
        $user = User::factory()->create(['name' => 'テスト太郎']);
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'image' => 'test_image.jpg',
        ]);

        $sellItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品テスト商品',
        ]);

        $buyItem = Item::factory()->create([
            'name' => '購入テスト商品',
        ]);

        Order::factory()->create([
            'user_id' => $user->id,
            'item_id' => $buyItem->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('profile.index', ['page' => 'sell']));
        $response->assertStatus(200);
        $response->assertSee('テスト太郎');
        $response->assertSee('出品テスト商品');
        $response->assertSee('プロフィール画像');

        $response = $this->get(route('profile.index', ['page' => 'buy']));
        $response->assertStatus(200);
        $response->assertSee('購入テスト商品');
    }
}
