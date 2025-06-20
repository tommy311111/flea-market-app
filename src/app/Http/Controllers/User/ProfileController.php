<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Item;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{

    public function edit()
    {
        $user = auth()->user();
    
        // プロフィールがまだ存在しなければ空で作成（1回だけ）
        if (!$user->profile) {
            $user->profile()->create([
                'image'     => null,
                'name'      => '',
                'postcode'  => '',
                'address'   => '',
                'building'  => '',
            ]);
        }
    
        $profile = $user->profile()->first();
    
        return view('user.edit', compact('user', 'profile'));
    }
    

    public function update(ProfileRequest $request)
    {
        $user = $request->user();
        $profile = $user->profile;
    
        $validated = $request->validated();
        $imagePath = $profile->image;
        
        if ($request->hasFile('image')) {
            
            $image = $request->file('image');
            
            if ($image->isValid()) {
                
                $originalName = $image->getClientOriginalName();
                $safeName = str_replace([' ', '+'], '_', $originalName);
        
                // ファイル名にユーザーIDやタイムスタンプを付与して一意に
                $filename = $user->id . '_' . time() . '_' . $safeName;
        
                // 保存してパスを取得
                $path = $image->storeAs('images/profiles', $filename, 'public');
                $imagePath = $filename;


            }
        }
        
        
    
        $user->update([
            'name' => $validated['name'],
        ]);
    
        $profile->update([
            'postcode' => $validated['postcode'] ?? '',
            'address' => $validated['address'] ?? '',
            'building' => $validated['building'] ?? '',
            'image' => $imagePath,
        ]);

        return redirect()->route('items.index')->with('status', 'プロフィールを更新しました');
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
