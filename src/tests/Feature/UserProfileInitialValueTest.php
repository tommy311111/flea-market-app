<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserProfileInitialValueTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function プロフィール変更後に入力欄に変更内容が初期値として表示される()
    {
        Storage::fake('public');

        // 1. 初期データを作成
        $user = User::factory()->create(['name' => '旧ユーザー名']);
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'postcode' => '111-1111',
            'address' => '旧住所',
            'building' => '旧ビル',
            'image' => 'old_image.jpg',
        ]);

        $this->actingAs($user);

        // 2. プロフィールを更新
        $this->put(route('profile.update'), [
            'name' => '新ユーザー名',
            'postcode' => '999-9999',
            'address' => '新しい住所',
            'building' => '新しいビル',
        ])->assertRedirect(route('profile.index'));

        // 3. 編集画面にアクセスして、フォームの初期値に更新内容が入っていることを確認
        $response = $this->get(route('profile.edit'));
        $response->assertStatus(200);

        // フォームの入力欄に更新後の情報が含まれているか（初期値として表示されるか）を検証
        $response->assertSee('value="新ユーザー名"', false);  // name
        $response->assertSee('value="999-9999"', false);      // postcode
        $response->assertSee('value="新しい住所"', false);     // address
        $response->assertSee('value="新しいビル"', false);     // building
    }
}
