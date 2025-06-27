<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function 商品名で部分一致検索ができる()
    {
        $itemA = Item::factory()->create([
            'name' => 'デジタルカメラ',
            'user_id' => $this->otherUser->id,
        ]);

        $itemB = Item::factory()->create([
            'name' => 'スマートフォン',
            'user_id' => $this->otherUser->id,
        ]);

        $response = $this->get('/?keyword=カメラ');

        $response->assertStatus(200);
        $response->assertSee('デジタルカメラ');
        $response->assertDontSee('スマートフォン');
    }

    /** @test */
    public function 検索状態がマイリストでも保持されている()
    {
        $likedItem = Item::factory()->create([
            'name' => 'カメラバッグ',
            'user_id' => $this->otherUser->id,
        ]);
        $this->user->likedItems()->attach($likedItem->id);

        $unmatchedItem = Item::factory()->create([
            'name' => 'スマホケース',
            'user_id' => $this->otherUser->id,
        ]);
        $this->user->likedItems()->attach($unmatchedItem->id);

        $searchResponse = $this->actingAs($this->user)
            ->get('/?keyword=カメラ');

        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('カメラバッグ');
        $searchResponse->assertDontSee('スマホケース');

        $mylistResponse = $this->get('/?page=mylist&keyword=カメラ');

        $mylistResponse->assertStatus(200);
        $mylistResponse->assertSee('カメラバッグ');
        $mylistResponse->assertDontSee('スマホケース');
    }
}
