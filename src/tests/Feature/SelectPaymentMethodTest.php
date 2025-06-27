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
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'postcode' => '123-4567',
            'address' => 'テスト市テスト町',
            'building' => 'テストビル',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->get(route('purchase.show', $item))
            ->assertStatus(200);

        $response = $this->post(route('purchase.savePaymentMethod', $item), [
            'payment_method' => 'カード支払い',
        ]);

        $response = $this->get(route('purchase.show', $item));
        $response->assertStatus(200);
        $response->assertSeeText('カード支払い');
    }
}
