<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報がすべて商品詳細ページに表示される()
    {
        $user = User::factory()->create();
        $categories = Category::factory()->count(2)->create();

        $item = Item::factory()->create([
            'user_id' => $user->id,
            'brand_name' => 'ブランドX',
            'price' => 12345,
            'condition' => '良好',
            'description' => '商品の説明テキストです。',
            'image' => 'test_image.jpg',
        ]);

        $item->categories()->attach($categories->pluck('id'));

        Like::factory()->count(3)->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);

        $commentUsers = User::factory()
            ->hasProfile()
            ->count(2)
            ->create();

        foreach ($commentUsers as $commentUser) {
            Comment::factory()->create([
                'item_id' => $item->id,
                'user_id' => $commentUser->id,
                'body' => 'コメント内容テスト',
            ]);
        }

        $response = $this->get(route('items.show', $item->id));
        $response->assertStatus(200);
        $response->assertSee('storage/images/items/' . $item->image);
        $response->assertSee($item->name);
        $response->assertSee('ブランドX');
        $response->assertSee(number_format($item->price));
        $response->assertSee('3');
        $response->assertSee('2');
        $response->assertSee('商品の説明テキストです。');

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }

        $response->assertSee('良好');
        $response->assertSee('コメント (2)');

        foreach ($commentUsers as $commentUser) {
            $response->assertSee($commentUser->name);
        }

        $response->assertSee('コメント内容テスト');
    }

    /** @test */
    public function 複数選択されたカテゴリが商品詳細ページに表示される()
    {
        $user = User::factory()->create();

        $categories = collect([
            'メンズ',
            'レディース',
            'キッズ',
        ])->map(fn($name) => Category::firstOrCreate(['name' => $name]));

        $item = Item::factory()->create(['user_id' => $user->id]);
        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get(route('items.show', $item->id));
        $response->assertStatus(200);

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}
