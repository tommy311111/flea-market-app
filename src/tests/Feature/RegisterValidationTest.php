<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\User;

class RegisterValidationTest extends TestCase
{
    use RefreshDatabase;
     /** @test */
     public function 名前が未入力だとバリデーションメッセージが表示される()
     {  
        $this->get('/register')->assertStatus(200);

         $response = $this->from('/register')->post('/register', [
             'name' => '',
             'email' => 'test@example.com',
             'password' => 'password123',
             'password_confirmation' => 'password123',
         ]);
 
         $response->assertRedirect('/register');
         $response->assertSessionHasErrors('name');
         $this->followRedirects($response)->assertSee('お名前を入力してください');
     }
 
     /** @test */
     public function メールアドレスが未入力だとバリデーションメッセージが表示される()
     {
        $this->get('/register')->assertStatus(200);

         $response = $this->from('/register')->post('/register', [
             'name' => 'テスト太郎',
             'email' => '',
             'password' => 'password123',
             'password_confirmation' => 'password123',
         ]);
 
         $response->assertRedirect('/register');
         $response->assertSessionHasErrors('email');
         $this->followRedirects($response)->assertSee('メールアドレスを入力してください');
     }
 
     /** @test */
     public function パスワードが未入力だとバリデーションメッセージが表示される()
     {
        $this->get('/register')->assertStatus(200);

         $response = $this->from('/register')->post('/register', [
             'name' => 'テスト太郎',
             'email' => 'test@example.com',
             'password' => '',
             'password_confirmation' => '',
         ]);
 
         $response->assertRedirect('/register');
         $response->assertSessionHasErrors('password');
         $this->followRedirects($response)->assertSee('パスワードを入力してください');
     }
 
     /** @test */
     public function パスワードが7文字以下だとバリデーションメッセージが表示される()
     {
        $this->get('/register')->assertStatus(200);

         $response = $this->from('/register')->post('/register', [
             'name' => 'テスト太郎',
             'email' => 'test@example.com',
             'password' => '1234567',
             'password_confirmation' => '1234567',
         ]);
 
         $response->assertRedirect('/register');
         $response->assertSessionHasErrors('password');
         $this->followRedirects($response)->assertSee('パスワードは8文字以上で入力してください');
     }
 
     /** @test */
     public function パスワードと確認用パスワードが一致しないとバリデーションメッセージが表示される()
     {
        $this->get('/register')->assertStatus(200);

         // 2. パスワードと確認用パスワードが異なる状態でPOST送信
    $response = $this->from('/register')->post('/register', [
        'name' => 'テスト太郎',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different123',
    ]);
 
         $response->assertRedirect('/register');
         $response->assertSessionHasErrors('password');
         $this->followRedirects($response)->assertSee('パスワードと一致しません');
     }
 
/** @test */
public function 正しい入力で会員登録後に認証済みとしてプロフィール編集画面へ遷移できる()
{
    $this->get('/register')->assertStatus(200);
    // ユーザー登録（ファクトリでもOK）
    $response = $this->post('/register', [
        'name' => 'テスト太郎',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    // ユーザー取得してメール認証済みにする
    $user = \App\Models\User::where('email', 'test@example.com')->first();
    $user->email_verified_at = now();
    $user->save();

    // ログイン状態にしてプロフィール編集画面にアクセス
    $this->actingAs($user);
    $response = $this->get('/mypage/profile');

    $response->assertStatus(200);
    $response->assertViewIs('user.edit'); // Bladeファイル名が user/edit.blade.php の場合
}


}
