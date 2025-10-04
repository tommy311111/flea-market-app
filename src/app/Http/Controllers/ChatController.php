<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatRequest;
use App\Models\Chat;
use App\Models\Order;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function start(Item $item)
    {
        $user = Auth::user();

        $existingOrder = Order::where('item_id', $item->id)
                              ->where('buyer_id', $user->id)
                              ->first();
        if ($existingOrder) {
            return redirect()->route('chats.show', $existingOrder->id);
        }

        $order = Order::create([
            'buyer_id' => $user->id,
            'seller_id' => $item->user_id,
            'item_id' => $item->id,
            'payment_method' => 'コンビニ払い',
            'status' => 'pending',
            'sending_postcode' => '000-0000',
            'sending_address' => '住所未設定',
            'sending_building' => '',
        ]);

        return redirect()->route('chats.show', $order->id);
    }

    public function show(Order $order)
    {
        $user = Auth::user();

        if ($order->buyer_id !== $user->id && $order->seller_id !== $user->id) {
            abort(403);
        }

        $chats = $order->chats()
                       ->with('sender.profile')
                       ->orderBy('created_at')
                       ->get();

        $order->chats()
              ->where('sender_id', '!=', $user->id)
              ->where('is_read', false)
              ->update(['is_read' => true]);

        $transactionOrders = Order::with(['item', 'chats'])
                                  ->where(function($q) use ($user) {
                                      $q->where('seller_id', $user->id)
                                        ->orWhere('buyer_id', $user->id);
                                  })
                                  ->where('status', 'pending')
                                  ->get();

        $profile = $user->profile;
        $order->load('item');

        $buyerRating = $order->ratings()->where('rater_id', $order->buyer_id)->first();
        $sellerRating = $order->ratings()->where('rater_id', $order->seller_id)->first();

        $canRate = false;
        if ($user->id === $order->seller_id && $buyerRating && !$sellerRating) {
            $canRate = true;
        }

        return view('user.chat', compact('order', 'chats', 'profile', 'transactionOrders', 'canRate'));
    }

    public function store(ChatRequest $request, Order $order)
    {
        $user = Auth::user();

        if ($order->buyer_id !== $user->id && $order->seller_id !== $user->id) {
            abort(403);
        }

        $data = $request->validated();
        $data['order_id'] = $order->id;
        $data['sender_id'] = $user->id;

        $chat = Chat::create($data);

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/images/chats', $imageName);
            $chat->image = 'images/chats/' . $imageName;
            $chat->save();
        }

        return redirect()->back()->with('success', 'メッセージを送信しました');
    }

    public function edit(Chat $chat)
    {
        $this->authorize('update', $chat);
        return response()->json($chat);
    }

    public function update(ChatRequest $request, Chat $chat)
    {
        $this->authorize('update', $chat);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/images/chats', $imageName);
            $data['image'] = $imageName;
        }

        $chat->update($data);

        return redirect()->back()->with('success', 'メッセージを更新しました');
    }

    public function destroy(Chat $chat)
    {
        $user = Auth::user();

        if ($chat->sender_id !== $user->id) {
            abort(403);
        }

        $chat->delete();

        return redirect()->back()->with('success', 'メッセージを削除しました');
    }
}
