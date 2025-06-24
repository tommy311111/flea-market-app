<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SelectPaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 小計画面で選択した支払い方法が正しく反映される()
    {
        // 1. ユーザー・プロフィール・商品作成
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'postcode' => '123-4567',
            'address' => 'テスト市テスト町',
            'building' => 'テストビル',
        ]);
        $item = Item::factory()->create();

        // 2. ログインして
        $this->actingAs($user);

        $this->get(route('purchase.show', $item))
        ->assertStatus(200);
        // 3. セレクトタグから支払い方法を選択（POST）
        $response = $this->post(route('purchase.savePaymentMethod', $item), [
            'payment_method' => 'カード支払い',
        ]);

        // 4. 同じ購入ページにリダイレクトされた後、支払い方法が反映されているか確認
        $response = $this->get(route('purchase.show', $item));
        $response->assertStatus(200);
        $response->assertSeeText('カード支払い'); // 画面上に表示されているかを確認
    }
}
