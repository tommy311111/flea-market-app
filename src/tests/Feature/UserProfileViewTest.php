<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileViewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 出品商品と購入商品がプロフィール画面で正しく表示される()
    {
        $user = User::factory()->create(['name' => '木村 春香']);
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'image' => 'test.jpg',
        ]);

        $sellItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'テスト出品商品',
        ]);

        $seller = User::factory()->create();
        $buyItem = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'テスト購入商品',
        ]);
        Order::factory()->completed()->create([
            'item_id' => $buyItem->id,
            'buyer_id' => $user->id,
            'seller_id' => $seller->id,
]);

        $this->actingAs($user);

        $responseSell = $this->get('/mypage');
        $responseSell->assertStatus(200)
            ->assertSee($user->name)
            ->assertSee('test.jpg')
            ->assertSee('テスト出品商品')
            ->assertDontSee('テスト購入商品');

        $responseBuy = $this->get('/mypage?page=buy');
        $responseBuy->assertStatus(200)
            ->assertSee($user->name)
            ->assertSee('test.jpg')
            ->assertSee('テスト購入商品')
            ->assertDontSee('テスト出品商品');
    }
}
