<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemExhibitionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品出品画面で必要な情報が正しく保存できる()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $category = Category::factory()->create([
            'name' => 'テストカテゴリー',
        ]);

        $image = UploadedFile::fake()->create('test_image.jpg', 100, 'image/jpeg');
        $safeName = str_replace([' ', '+'], '_', $image->getClientOriginalName());
        $image->storeAs('images/items', $safeName, 'public');

        $this->actingAs($user);

        $response = $this->get(route('items.create'));
        $response->assertStatus(200);

        $postData = [
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト商品です。',
            'price' => 1200,
            'condition' => '良好',
            'category' => [$category->id],
            'image' => $image,
        ];

        $response = $this->post(route('items.store'), $postData);
        $response->assertRedirect('/');

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト商品です。',
            'price' => 1200,
            'condition' => '良好',
            'image' => $safeName,
            'user_id' => $user->id,
        ]);

        $item = Item::latest()->first();
        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $category->id,
        ]);

        Storage::disk('public')->assertExists('images/items/' . $safeName);
    }
}
