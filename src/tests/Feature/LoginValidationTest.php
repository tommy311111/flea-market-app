<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function メールアドレスが未入力だとバリデーションメッセージが表示される()
{
    $this->get('/login')->assertStatus(200); // ログインページを開く

    $response = $this->post('/login', [
        'email' => '',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors([
        'email' => 'メールアドレスを入力してください',
    ]);
}


    /** @test */
    public function パスワードが未入力だとバリデーションメッセージが表示される()
{
    $this->get('/login')->assertStatus(200); // ログインページを開く

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => '',
    ]);

    $response->assertSessionHasErrors([
        'password' => 'パスワードを入力してください',
    ]);
}


    /** @test */
    public function 入力情報が誤っているとログインエラーが表示される()
{
    $response = $this->from('/login')->post('/login', [
        'email' => 'wrong@example.com',
        'password' => 'invalidpassword',
    ]);

    // リダイレクトを確認
    $response->assertRedirect('/login');

    // セッションエラーを確認
    $this->assertGuest();
    $this->assertTrue(session()->has('errors'));

    // リダイレクト先のページでメッセージを確認
    $followUp = $this->get('/login');
    $followUp->assertSee('ログイン情報が登録されていません');
}

    /** @test */
    public function 正しい情報を入力すればログインできる()
    {
        $this->get('/login')->assertStatus(200); // ログインページを開く
        // 事前にユーザーを登録
        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(), // Fortifyではメール認証済みが必要
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/'); // ログイン後に遷移するトップページ等
    }
}
