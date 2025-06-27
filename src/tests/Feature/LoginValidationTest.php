<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function メールアドレスが未入力だとバリデーションメッセージが表示される()
    {
        $this->get('/login')->assertStatus(200);

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
        $this->get('/login')->assertStatus(200);

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

        $response->assertRedirect('/login');

        $this->assertGuest();
        $this->assertTrue(session()->has('errors'));

        $followUp = $this->get('/login');
        $followUp->assertSee('ログイン情報が登録されていません');
    }

    /** @test */
    public function 正しい情報を入力すればログインできる()
    {
        $this->get('/login')->assertStatus(200);

        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/');
    }
}
