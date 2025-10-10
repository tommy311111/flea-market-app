<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
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

    public function index(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;
        $page = $request->query('page', 'sell');

        $items = collect();
        $orders = collect();

        $transactionOrders = Order::with(['chats'])
            ->where(function($q) use ($user) {
                $q->where('seller_id', $user->id)
                  ->orWhere('buyer_id', $user->id);
            })
            ->where('status', 'in_progress')
            ->get();

        $total_new_messages = $transactionOrders->sum(function($order) use ($user) {
            return $order->chats
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->count();
        });

        if ($page === 'buy') {
            $orders = Order::where('buyer_id', $user->id)
                ->whereIn('status', ['in_progress', 'completed'])
                ->with('item')
                ->latest()
                ->get();

            $items = $orders->pluck('item')->filter();

        } elseif ($page === 'transaction') {
            $orders = $transactionOrders->load(['item', 'chats']);

            foreach ($orders as $order) {
                $order->new_messages_count = $order->chats
                    ->where('sender_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->count();
                $order->last_message_at = $order->chats->max('created_at');
            }

            $orders = $orders->sortByDesc(function ($order) {
                return $order->last_message_at ?? now();
            });

            $items = $orders->pluck('item')->filter();

        } else {
            $items = $user->items()->latest()->get();
            $orders = Order::whereIn('item_id', $items->pluck('id'))->get();
        }

        return view('user.profile', compact(
            'user',
            'profile',
            'items',
            'page',
            'orders',
            'total_new_messages'
        ));
    }
}
