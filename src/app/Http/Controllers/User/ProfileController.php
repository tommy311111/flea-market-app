<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Actions\Fortify\UpdateUserProfileInformation; // 追加忘れずに

class ProfileController extends Controller
{

public function edit()
{
    $user = auth()->user();

    if (!$user->profileIsFilled()) {
        return redirect()->route('register.profile.form');
    }

    return view('user.edit', compact('user'));
}

public function update(Request $request, UpdateUserProfileInformation $updater)
{
    $input = $request->all();

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('images/profiles', 'public');
        $input['image'] = $path;
    } else {
        // 画像未更新なら入力配列から削除（DBの値を維持させるため）
        unset($input['image']);
    }

    $updater->update($request->user(), $input);

    return redirect()->route('mypage.profile.edit')->with('status', 'プロフィールを更新しました');
}


}
