<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報がすべて商品詳細ページに表示される()
    {
        // ユーザー作成
        $user = User::factory()->create();

        // カテゴリ複数作成（Seederのカテゴリー名から作る）
        $categories = Category::factory()->count(2)->create();

        // 商品作成
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'brand_name' => 'ブランドX',
            'price' => 12345,
            'condition' => '良好',
            'description' => '商品の説明テキストです。',
            'image' => 'test_image.jpg',
        ]);

        // 商品にカテゴリを複数紐づけ
        $item->categories()->attach($categories->pluck('id'));

        // いいねを3件作成
        Like::factory()->count(3)->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);

        // コメントを2件作成（ユーザーは別途作成）
        $commentUsers = User::factory()
        ->hasProfile()  // ✅ プロフィールも一緒に作成する
        ->count(2)
        ->create();
        foreach ($commentUsers as $commentUser) {
            Comment::factory()->create([
                'item_id' => $item->id,
                'user_id' => $commentUser->id,
                'body' => 'コメント内容テスト',
            ]);
        }

        // 商品詳細ページへアクセス
        $response = $this->get(route('items.show', $item->id));

        $response->assertStatus(200);

        // 1. 商品画像
        $response->assertSee('storage/images/items/' . $item->image);

        // 2. 商品名
        $response->assertSee($item->name);

        // 3. ブランド名
        $response->assertSee('ブランドX');

        // 4. 価格（カンマ付き）
        $response->assertSee(number_format($item->price));

        // 5. いいね数
        $response->assertSee('3');

        // 6. コメント数
        $response->assertSee('2');

        // 7. 商品説明
        $response->assertSee('商品の説明テキストです。');

        // 8. カテゴリ名（複数あるので両方確認）
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }

        // 9. 商品の状態
        $response->assertSee('良好');

        // 10. コメント数（タイトル部分）
        $response->assertSee('コメント (2)');

        // 11. コメントしたユーザー名
        foreach ($commentUsers as $commentUser) {
            $response->assertSee($commentUser->name);
        }

        // 12. コメント内容
        $response->assertSee('コメント内容テスト');
    }

    /** @test */
    public function 複数選択されたカテゴリが商品詳細ページに表示される()
    {
        $user = User::factory()->create();

        $categories = Category::factory()->count(3)->create();

        $item = Item::factory()->create(['user_id' => $user->id]);

        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get(route('items.show', $item->id));

        $response->assertStatus(200);

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}
