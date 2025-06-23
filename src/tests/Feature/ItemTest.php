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
        // Arrange
        Item::factory()->count(3)->create();

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200);
        foreach (Item::all() as $item) {
            $response->assertSee($item->name);
        }
    }

    /** @test */
    public function 購入済み商品にはSoldと表示される()
    {
        // Arrange
        $item = Item::factory()->create();
        Order::factory()->create([
            'item_id' => $item->id,
        ]);

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Sold');

         // 商品名の直後にSoldが表示されていることを確認
        $response->assertSeeInOrder([
        $item->name,
        'Sold',
    ]);
    }

    /** @test */
    public function 自分が出品した商品は一覧に表示されない()
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $myItem = Item::factory()->create(['user_id' => $user->id]);
        $otherItem = Item::factory()->create(['user_id' => $otherUser->id]);

        // Act
        $response = $this->actingAs($user)->get('/');

        // Assert
        $response->assertStatus(200);
        $response->assertDontSee($myItem->name);     // 自分の出品商品は見えない
        $response->assertSee($otherItem->name);      // 他人の商品は表示される
    }
}
