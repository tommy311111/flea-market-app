<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        if (!$user->profile) {
            $user->profile()->create([
                'image'    => null,
                'name'     => '',
                'postcode' => '',
                'address'  => '',
                'building' => '',
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
                $filename = $user->id . '_' . time() . '_' . $safeName;
                $path = $image->storeAs('images/profiles', $filename, 'public');
                $imagePath = $filename;
            }
        }

        $user->update([
            'name' => $validated['name'],
        ]);

        $profile->update([
            'postcode' => $validated['postcode'] ?? '',
            'address'  => $validated['address'] ?? '',
            'building' => $validated['building'] ?? '',
            'image'    => $imagePath,
        ]);

        return redirect()->route('profile.index')->with('status', 'プロフィールを更新しました');
    }

    public function index(\Illuminate\Http\Request $request)
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
