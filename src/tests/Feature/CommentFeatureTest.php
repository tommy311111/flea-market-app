<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン済みのユーザーはコメントを送信できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)
            ->post(route('comment.store', $item->id), [
                'body' => 'これはテストコメントです',
            ])
            ->assertRedirect(); // 成功時のリダイレクト先がある前提

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => 'これはテストコメントです',
        ]);

        // 再取得して、コメント数が1で表示されているか確認
        $response = $this->get(route('items.show', $item->id));
        $response->assertSee('コメント (1)');
    }

    /** @test */
    public function ログイン前のユーザーはコメントを送信できない()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('comment.store', $item->id), [
            'body' => 'ゲストユーザーのコメント',
        ]);

        $response->assertRedirect(route('login')); // ゲストはログインページへリダイレクト
        $this->assertDatabaseMissing('comments', [
            'body' => 'ゲストユーザーのコメント',
        ]);
    }

    /** @test */
    public function コメントが未入力の場合バリデーションメッセージが表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('items.show', $item->id))
            ->post(route('comment.store', $item->id), [
                'body' => '',
            ]);

        $response->assertRedirect(route('items.show', $item->id));
        $response->assertSessionHasErrors('body');
    }

    /** @test */
    public function コメントが255字を超えるとバリデーションメッセージが表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $longText = str_repeat('あ', 256);

        $response = $this->actingAs($user)
            ->from(route('items.show', $item->id))
            ->post(route('comment.store', $item->id), [
                'body' => $longText,
            ]);

        $response->assertRedirect(route('items.show', $item->id));
        $response->assertSessionHasErrors('body');

    }
}
