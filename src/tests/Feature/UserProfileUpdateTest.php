<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserProfile;

class UserProfileUpdateTest extends TestCase
{
    /** @test */
public function ユーザー情報を更新するとデータベースと編集画面に正しく反映される()
{
    // 1. 初期ユーザーとプロフィール作成
    $user = User::factory()->create(['name' => '旧ユーザー名']);
    UserProfile::factory()->create([
        'user_id' => $user->id,
        'postcode' => '123-4567',
        'address' => '旧住所',
        'building' => '旧ビル',
    ]);

    $this->actingAs($user);

    // 2. 情報を更新
    $this->put(route('profile.update'), [
        'name' => '新ユーザー名',
        'postcode' => '999-9999',
        'address' => '新しい市町村',
        'building' => '新しいビル',
    ])->assertRedirect(route('profile.edit'));

    // 3. データベースに反映されているか確認
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => '新ユーザー名',
    ]);

    $this->assertDatabaseHas('user_profiles', [
        'user_id' => $user->id,
        'postcode' => '999-9999',
        'address' => '新しい市町村',
        'building' => '新しいビル',
    ]);

    // 4. 編集画面に表示されているか確認
    $response = $this->get(route('profile.edit'));
    $response->assertStatus(200);
    $response->assertSee('新ユーザー名');
    $response->assertSee('999-9999');
    $response->assertSee('新しい市町村');
    $response->assertSee('新しいビル');
}

}
