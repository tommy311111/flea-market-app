<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねアイコン押下でいいねが登録されいいね数が画面に反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->get("/items/{$item->id}");
        $response->assertStatus(200);
        $response->assertSee('<span class="item-detail__count">0</span>', false);

        $this->actingAs($user)
            ->post("/items/{$item->id}/like");

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'deleted_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->get("/items/{$item->id}");
        $response->assertStatus(200);
        $response->assertSee('<span class="item-detail__count">1</span>', false);
    }

    /** @test */
    public function 追加済みのアイコンは色が変化する()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)
            ->get("/items/{$item->id}")
            ->assertStatus(200);

        $this->actingAs($user)
            ->post("/items/{$item->id}/like");

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function アイコン再押下でいいね解除()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)
            ->get("/items/{$item->id}")
            ->assertStatus(200);

        $this->actingAs($user)->post("/items/{$item->id}/like");
        $this->assertEquals(1, Like::count());

        $this->actingAs($user)->post("/items/{$item->id}/like");

        $this->assertSoftDeleted('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertEquals(0, Like::whereNull('deleted_at')->count());

        $response = $this->actingAs($user)
            ->get("/items/{$item->id}");
        $response->assertStatus(200);
        $response->assertSee('<span class="item-detail__count">0</span>', false);
    }
}
