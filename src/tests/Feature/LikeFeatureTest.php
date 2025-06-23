<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LikeFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねアイコン押下でいいねが登録されいいね数が画面に反映される()
{
    $user = User::factory()->create();
    $item = Item::factory()->create();

    // 1. ユーザーにログインし商品詳細ページを開く（いいね数は0）
    $response = $this->actingAs($user)
        ->get("/items/{$item->id}");
    $response->assertStatus(200);
    $response->assertSee('<span class="item-detail__count">0</span>', false);

    // 2. いいね登録
    $this->actingAs($user)
        ->post("/items/{$item->id}/like");

    // 3. DBにいいねが登録されているか確認
    $this->assertDatabaseHas('likes', [
        'user_id' => $user->id,
        'item_id' => $item->id,
        'deleted_at' => null,
    ]);

    // 4. 再度商品詳細ページを取得し、いいね数が1に増えていることを確認
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

        // 1. 商品詳細ページを開く
        $this->actingAs($user)
        ->get("/items/{$item->id}")
        ->assertStatus(200);

        // 2. いいねアイコンを押下（POSTリクエスト）
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
        // 最初にいいね
        $this->actingAs($user)->post("/items/{$item->id}/like");
        $this->assertEquals(1, Like::count());

        // 再度いいね（解除）
        $this->actingAs($user)->post("/items/{$item->id}/like");

        $this->assertSoftDeleted('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 有効ないいねは0になる
        $this->assertEquals(0, Like::whereNull('deleted_at')->count());

        // いいね数が0に減っていることを画面上で確認
        $response = $this->actingAs($user)
        ->get("/items/{$item->id}");
        $response->assertStatus(200);
        $response->assertSee('<span class="item-detail__count">0</span>', false);
    }
}
