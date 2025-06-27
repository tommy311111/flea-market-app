<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        $this->likedItem = Item::factory()->create(['user_id' => $this->otherUser->id]);
        $this->soldItem = Item::factory()->create(['user_id' => $this->otherUser->id]);
        $this->myItem = Item::factory()->create(['user_id' => $this->user->id]);

        $this->user->likedItems()->attach($this->likedItem->id);
        $this->user->likedItems()->attach($this->soldItem->id);

        Order::factory()->create([
            'user_id' => $this->user->id,
            'item_id' => $this->soldItem->id,
        ]);
    }

    /** @test */
    public function いいねした商品だけが表示される()
    {
        $response = $this->actingAs($this->user)
            ->get(route('items.index', ['page' => 'mylist']));

        $response->assertStatus(200);
        $response->assertSeeText($this->likedItem->name);
        $response->assertSeeText($this->soldItem->name);
        $response->assertDontSeeText($this->myItem->name);
    }

    /** @test */
    public function 購入済み商品には_sold_ラベルが表示される()
    {
        $response = $this->actingAs($this->user)
            ->get(route('items.index', ['page' => 'mylist']));

        $response->assertStatus(200);
        $response->assertSeeText($this->soldItem->name);
        $response->assertSee('Sold');
    }

    /** @test */
    public function 自分が出品した商品は表示されない()
    {
        $response = $this->actingAs($this->user)
            ->get(route('items.index', ['page' => 'mylist']));

        $response->assertStatus(200);
        $response->assertDontSeeText($this->myItem->name);
    }

    /** @test */
    public function 未認証の場合は何も表示されない()
    {
        $response = $this->get(route('items.index', ['page' => 'mylist']));

        $response->assertStatus(200);
        $response->assertSee('マイリストを見るにはログインが必要です');
        $response->assertDontSeeText($this->likedItem->name);
        $response->assertDontSeeText($this->soldItem->name);
        $response->assertDontSeeText($this->myItem->name);
    }
}
