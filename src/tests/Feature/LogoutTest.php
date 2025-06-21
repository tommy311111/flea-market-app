<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログアウトができる()
    {
        // テストユーザー作成＆ログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // ログアウト処理（POST /logout）を実行
        $response = $this->post(route('logout'));

        // ログアウト後、ホームなどへのリダイレクト確認（実装に応じて変更）
        $response->assertRedirect('/');

        // 認証が解除されていることを確認
        $this->assertGuest();
    }
}
