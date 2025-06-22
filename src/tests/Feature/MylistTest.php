<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用ユーザー作成
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        // 商品3つ作成
        // 1. $this->otherUserが出品し、$this->userがいいねした商品（未購入）
        $this->likedItem = Item::factory()->create(['user_id' => $this->otherUser->id]);

        // 2. $this->otherUserが出品し、$this->userがいいねし、購入済みの商品
        $this->soldItem = Item::factory()->create(['user_id' => $this->otherUser->id]);

        // 3. $this->userが出品した商品（自分の商品）
        $this->myItem = Item::factory()->create(['user_id' => $this->user->id]);

        // いいね登録（$this->userが1と2にいいね）
        $this->user->likedItems()->attach($this->likedItem->id);
        $this->user->likedItems()->attach($this->soldItem->id);


        // 購入済みの設定（Order作成）
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

        // いいねした商品の名前は見える
        $response->assertSeeText($this->likedItem->name);
        $response->assertSeeText($this->soldItem->name);

        // 自分が出品した商品は表示されない
        $response->assertDontSeeText($this->myItem->name);
    }

    /** @test */
    public function 購入済み商品には_sold_ラベルが表示される()
    {
        $response = $this->actingAs($this->user)
            ->get(route('items.index', ['page' => 'mylist']));

        $response->assertStatus(200);

        // soldItemの商品名表示あり
        $response->assertSeeText($this->soldItem->name);

        // 「Sold」ラベルの表示をチェック
        $response->assertSee('Sold');
    }

    /** @test */
    public function 自分が出品した商品は表示されない()
    {
        $response = $this->actingAs($this->user)
            ->get(route('items.index', ['page' => 'mylist']));

        $response->assertStatus(200);

        // 自分の商品は見えない
        $response->assertDontSeeText($this->myItem->name);
    }

    /** @test */
    public function 未認証の場合は何も表示されない()
    {
        $response = $this->get(route('items.index', ['page' => 'mylist']));

        $response->assertStatus(200);

        // ログインしていないので空メッセージが表示される
        $response->assertSee('マイリストを見るにはログインが必要です');

        // 商品名は見えない
        $response->assertDontSeeText($this->likedItem->name);
        $response->assertDontSeeText($this->soldItem->name);
        $response->assertDontSeeText($this->myItem->name);
    }
}
