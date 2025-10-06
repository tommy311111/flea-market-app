<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Rating;
use App\Mail\OrderCompletedMail;
use Illuminate\Support\Facades\Mail;

class RatingController extends Controller
{
    public function store(Request $request, Order $order)
    {
        $request->validate([
            'score' => 'required|integer|min:1|max:5',
        ]);

        if (Rating::where('order_id', $order->id)
                  ->where('rater_id', auth()->id())
                  ->exists()) {
            return back()->with('error', 'この取引はすでに評価済みです');
        }

        Rating::create([
            'order_id' => $order->id,
            'rater_id' => auth()->id(),
            'rated_id' => $order->seller_id == auth()->id() ? $order->buyer_id : $order->seller_id,
            'score'    => $request->score,
        ]);

        if ($order->status === 'pending') {
            $order->update(['status' => 'completed']);
        }

        if ($order->buyer_id === auth()->id()) {
        Mail::to($order->seller->email)->send(new OrderCompletedMail($order));
        }

        return redirect()->route('items.index')->with('success', '評価を送信しました');
    }
}
