<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Item;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Actions\Fortify\UpdateUserProfileInformation; // 追加忘れずに

class ProfileController extends Controller
{

public function edit()
{
    $user = auth()->user();
    $profile = $user->profile;

    if (!$user->profileIsFilled()) {
        return redirect()->route('register.profile.form');
    }

    return view('user.edit', compact('user', 'profile'));
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

// 既存の edit(), update() に続けて追加してOK
public function index(Request $request)
{
    $user = Auth::user();
    $profile = $user->profile;

    $page = $request->query('page', 'sell');

    if ($page === 'buy') {
        $items = Order::with('item')
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->pluck('item');
    } else {
        $items = Item::where('user_id', $user->id)
            ->latest()
            ->get();
    }

    return view('user.profile', compact('user', 'profile', 'items', 'page'));
}


}
