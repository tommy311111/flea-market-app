<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
public function 購入ボタンを押下すると購入が完了する()
{
    // 1. ログインする
    $user = User::factory()->create();
    UserProfile::factory()->create([
        'user_id' => $user->id,
        'postcode' => '123-4567',
        'address' => 'テスト市テスト町',
        'building' => 'テストビル',
    ]);
    $user->load('profile'); // リレーション読み込み

    $item = Item::factory()->create();

    $this->actingAs($user);

    // 2. 購入画面を開く
    $this->get(route('purchase.show', $item))
        ->assertStatus(200);

    // 3. セレクトタグから支払い方法を選択して保存（POST）
    $response = $this->post(route('purchase.savePaymentMethod', $item), [
        'payment_method' => 'カード支払い',
    ]);

    // 4. 保存後、購入ページにリダイレクトされるか
    $response->assertRedirect(route('purchase.show', $item));

    // 5. 「購入する」ボタン押下（POST）
    $purchaseResponse = $this->post(route('purchase.store', $item), [
        'payment_method' => 'カード支払い',
        'sending_postcode'   => $user->profile->postcode,
        'sending_address'    => $user->profile->address,
        'sending_building'   => $user->profile->building, // PurchaseRequestのルールに沿う必要あり
    ]);

    // 6. 購入完了 → route('items.index') にリダイレクトされるか
    $purchaseResponse->assertRedirect(route('items.index'));

    // 購入が保存されているか
    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'item_id' => $item->id,
        'payment_method' => 'カード支払い',
    ]);
}

/** @test */
public function 購入した商品は商品一覧画面でSoldと表示される()
{
    $user = User::factory()->create();
    UserProfile::factory()->create([
        'user_id' => $user->id,
        'postcode' => '123-4567',
        'address' => 'テスト市テスト町',
        'building' => 'テストビル',
    ]);
    $user->load('profile');

    $item = Item::factory()->create();

    // 1. ログインして
    $this->actingAs($user);

    // 2. 購入画面を開く
    $this->get(route('purchase.show', $item))
        ->assertStatus(200);

    // 3. 支払い方法を選択・保存
    $this->post(route('purchase.savePaymentMethod', $item), [
        'payment_method' => 'カード支払い',
    ])->assertRedirect(route('purchase.show', $item));

    // 4. 「購入する」ボタン押下（購入処理）
    $this->post(route('purchase.store', $item), [
        'payment_method' => 'カード支払い',
        'sending_postcode' => $user->profile->postcode,
        'sending_address' => $user->profile->address,
        'sending_building' => $user->profile->building,
    ])->assertRedirect(route('items.index'));

    // 5. 商品一覧画面に「Sold」が表示されるか確認
    $response = $this->get(route('items.index'));
    $response->assertStatus(200);
    $response->assertSeeText($item->name);
    $response->assertSeeText('Sold');
}


    /** @test */
    public function 購入した商品が購入一覧に表示される()
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'postcode' => '123-4567',
            'address' => 'テスト市テスト町',
            'building' => 'テストビル',
        ]);
        $user->load('profile');
    
        $item = Item::factory()->create();
    
        // 1. ログインして
        $this->actingAs($user);
    
        // 2. 購入画面を開く
        $this->get(route('purchase.show', $item))
            ->assertStatus(200);
    
        // 3. 支払い方法を選択・保存
        $this->post(route('purchase.savePaymentMethod', $item), [
            'payment_method' => 'カード支払い',
        ])->assertRedirect(route('purchase.show', $item));
    
        // 4. 「購入する」ボタン押下（購入処理）
        $this->post(route('purchase.store', $item), [
            'payment_method' => 'カード支払い',
            'sending_postcode' => $user->profile->postcode,
            'sending_address' => $user->profile->address,
            'sending_building' => $user->profile->building,
        ])->assertRedirect(route('items.index'));

        // プロフィールの「購入した商品」タブを確認
        $response = $this->actingAs($user)
            ->get(route('profile.index', ['page' => 'buy']));

        $response->assertStatus(200);
        $response->assertSee($item->name);
    }
}
