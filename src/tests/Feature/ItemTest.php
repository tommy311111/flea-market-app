<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全商品が一覧表示される()
    {
        Item::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        foreach (Item::all() as $item) {
            $response->assertSee($item->name);
        }
    }

    /** @test */
    public function 購入済み商品にはSoldと表示される()
    {
        $item = Item::factory()->create();
        Order::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => User::factory(),
            'seller_id' => $item->user_id,
            'status' => 'completed',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sold');
        $response->assertSeeInOrder([
            $item->name,
            'Sold',
        ]);
    }

    /** @test */
    public function 自分が出品した商品は一覧に表示されない()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Item::factory()->create([
            'user_id' => $user->id,
            'name' => '自分の商品タイトル',
        ]);
        Item::factory()->create([
            'user_id' => $otherUser->id,
            'name' => '他人の商品タイトル',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('自分の商品タイトル');
        $response->assertSee('他人の商品タイトル');
    }
}
