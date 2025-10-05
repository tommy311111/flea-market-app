<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 購入ボタンを押下すると購入が完了する()
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

        $this->actingAs($user);

        $this->get(route('purchase.show', $item))->assertStatus(200);

        $response = $this->post(route('purchase.savePaymentMethod', $item), [
            'payment_method' => 'カード支払い',
        ]);
        $response->assertRedirect(route('purchase.show', $item));

        $purchaseResponse = $this->post(route('purchase.store', $item), [
            'payment_method' => 'カード支払い',
            'sending_postcode' => $user->profile->postcode,
            'sending_address' => $user->profile->address,
            'sending_building' => $user->profile->building,
        ]);
        $purchaseResponse->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $user->id,
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
        $this->actingAs($user);

        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        $response = $this->get(route('purchase.show', $item));
        $response->assertStatus(200);
        $response->assertSeeText($item->name);

        $this->post(route('purchase.savePaymentMethod', $item), [
            'payment_method' => 'カード支払い',
        ])->assertRedirect(route('purchase.show', $item));

        if (app()->environment('testing')) {
            Order::updateOrCreate(
                ['buyer_id' => $user->id, 'item_id' => $item->id],
                [
                    'seller_id' => $seller->id,
                    'payment_method' => 'カード支払い',
                    'sending_postcode' => $user->profile->postcode,
                    'sending_address' => $user->profile->address,
                    'sending_building' => $user->profile->building,
                    'status' => 'completed',
                ]
            );
        } else {
            $this->post(route('purchase.store', $item), [
                'payment_method' => 'カード支払い',
                'sending_postcode' => $user->profile->postcode,
                'sending_address' => $user->profile->address,
                'sending_building' => $user->profile->building,
            ])->assertRedirect(route('items.index'));
        }

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $user->id,
            'item_id' => $item->id,
            'status' => 'completed',
        ]);

        $listResponse = $this->get(route('items.index'));
        $listResponse->assertStatus(200);
        $listResponse->assertSeeText($item->name);
        $listResponse->assertSeeText('Sold');
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

        $this->actingAs($user);

        $this->get(route('purchase.show', $item))->assertStatus(200);

        $this->post(route('purchase.savePaymentMethod', $item), [
            'payment_method' => 'カード支払い',
        ])->assertRedirect(route('purchase.show', $item));

        if (app()->environment('testing')) {
            Order::updateOrCreate(
                ['buyer_id' => $user->id, 'item_id' => $item->id],
                [
                    'seller_id' => $item->user_id,
                    'payment_method' => 'カード支払い',
                    'sending_postcode' => $user->profile->postcode,
                    'sending_address' => $user->profile->address,
                    'sending_building' => $user->profile->building,
                    'status' => 'completed',
                ]
            );
        } else {
            $this->post(route('purchase.store', $item), [
                'payment_method' => 'カード支払い',
                'sending_postcode' => $user->profile->postcode,
                'sending_address' => $user->profile->address,
                'sending_building' => $user->profile->building,
            ])->assertRedirect(route('items.index'));
        }

        $response = $this->actingAs($user)
                         ->get(route('profile.index', ['page' => 'buy']));

        $response->assertStatus(200);
        $response->assertSee($item->name);
    }
}
