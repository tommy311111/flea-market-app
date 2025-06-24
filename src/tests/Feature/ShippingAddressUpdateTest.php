<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShippingAddressUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 登録した住所が購入画面に反映される()
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'postcode' => '000-0000',
            'address' => '旧住所',
            'building' => '旧ビル',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        // 送付先住所を更新する
        $this->post(route('purchase.address.update', $item), [
            'postcode' => '123-4567',
            'address' => '新しい市町村',
            'building' => '新しい建物',
        ])->assertRedirect(route('purchase.show', $item));

        // 再度商品購入画面を開く
        $response = $this->get(route('purchase.show', $item));

        // 更新した住所が画面に反映されていることを確認
        $response->assertSeeText('123-4567');
        $response->assertSeeText('新しい市町村');
        $response->assertSeeText('新しい建物');
    }

    /** @test */
    public function 購入した商品に送付先住所が紐づく()
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'postcode' => '111-1111',
            'address' => '初期住所',
            'building' => '初期ビル',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        // 支払い方法を保存
        $this->post(route('purchase.savePaymentMethod', $item), [
            'payment_method' => 'コンビニ払い',
        ]);

        // 送付先住所を変更
        $this->post(route('purchase.address.update', $item), [
            'postcode' => '222-2222',
            'address' => '変更後の住所',
            'building' => '変更後の建物',
        ]);

        // 購入処理を実行
        $this->post(route('purchase.store', $item), [
            'payment_method' => 'コンビニ払い',
            'sending_postcode' => '222-2222',
            'sending_address' => '変更後の住所',
            'sending_building' => '変更後の建物',
        ])->assertRedirect(route('items.index'));

        // 購入テーブルに正しく保存されたか確認
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'sending_postcode' => '222-2222',
            'sending_address' => '変更後の住所',
            'sending_building' => '変更後の建物',
        ]);
    }
}
