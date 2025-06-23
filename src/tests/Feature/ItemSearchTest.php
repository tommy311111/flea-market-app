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

    protected function setUp(): void
    {
        parent::setUp();
        // ユーザー作成 & ログイン
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create(); // ← 修正：プロパティに保存
        $this->actingAs($this->user);
    }

    /** @test */
    public function 商品名で部分一致検索ができる()
    {
        // 商品A（検索対象）
        $itemA = Item::factory()->create([
            'name' => 'デジタルカメラ',
            'user_id' => $this->otherUser->id, // 他人の商品
        ]);

        // 商品B（ヒットしない）
        $itemB = Item::factory()->create([
            'name' => 'スマートフォン',
            'user_id' => $this->otherUser->id,
        ]);

        // 検索実行
        $response = $this->get('/?keyword=カメラ');

        $response->assertStatus(200);
        $response->assertSee('デジタルカメラ');
        $response->assertDontSee('スマートフォン');
    }

    /** @test */
    public function 検索状態がマイリストでも保持されている()
    {
        // 商品A：カメラ、いいね済み
    $likedItem = Item::factory()->create([
        'name' => 'カメラバッグ',
        'user_id' => $this->otherUser->id,
    ]);
    $this->user->likedItems()->attach($likedItem->id);

    // 商品B：スマホ、いいね済みだが検索にヒットしない
    $unmatchedItem = Item::factory()->create([
        'name' => 'スマホケース',
        'user_id' => $this->otherUser->id,
    ]);
    $this->user->likedItems()->attach($unmatchedItem->id);

    // 1. 商品一覧ページで検索（キーワード: カメラ）
    $searchResponse = $this->actingAs($this->user)
        ->get('/?keyword=カメラ');

    $searchResponse->assertStatus(200);
    $searchResponse->assertSee('カメラバッグ');
    $searchResponse->assertDontSee('スマホケース');

    // 2. 検索キーワードを保持したままマイリストページに遷移
    $mylistResponse = $this->get('/?page=mylist&keyword=カメラ');

    $mylistResponse->assertStatus(200);
    $mylistResponse->assertSee('カメラバッグ');
    $mylistResponse->assertDontSee('スマホケース');
    }
}