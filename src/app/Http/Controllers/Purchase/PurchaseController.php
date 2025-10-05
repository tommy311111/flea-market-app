<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function savePaymentMethod(Request $request, Item $item)
    {
        $request->validate([
            'payment_method' => 'required|in:コンビニ払い,カード支払い',
        ]);

        $user = Auth::user();

        $order = Order::firstOrNew([
            'buyer_id' => $user->id,
            'item_id'  => $item->id,
        ]);

        $order->seller_id         = $item->user_id;
        $order->payment_method   = $request->payment_method;
        $order->sending_postcode = $user->profile->postcode;
        $order->sending_address  = $user->profile->address;
        $order->sending_building = $user->profile->building;
        $order->status           = 'pending';
        $order->save();

        return redirect()
            ->route('purchase.show', $item->id)
            ->with('selectedPayment', $request->payment_method);
    }

    public function show(Item $item)
    {
        $user = Auth::user();
        $methods = Order::PAYMENT_METHOD;

        $selectedPayment = session('selectedPayment')
            ?? Order::where('buyer_id', $user->id)
                ->where('item_id', $item->id)
                ->value('payment_method');

        return view('items.purchase', compact('item', 'user', 'methods', 'selectedPayment'));
    }

    public function store(PurchaseRequest $request, Item $item)
    {
        $user = Auth::user();

        if (app()->environment('testing')) {
            Order::updateOrCreate(
                ['buyer_id' => $user->id, 'item_id' => $item->id],
                [
                    'seller_id'        => $item->user_id,
                    'payment_method'   => $request->payment_method,
                    'sending_postcode' => $user->profile->postcode,
                    'sending_address'  => $user->profile->address,
                    'sending_building' => $user->profile->building,
                    'status'           => 'pending',
                ]
            );

            return redirect()->route('items.index')
                ->with('success', '購入テスト完了（モック）');
        }

        $stripe = new \Stripe\StripeClient(config('stripe.stripe_secret_key'));
        $checkout_session = $stripe->checkout->sessions->create([
            'payment_method_types' => $request->payment_method === 'コンビニ払い'
                ? ['konbini']
                : ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount'  => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('purchase.success', ['item' => $item->id]),
            'cancel_url'  => route('purchase.show', ['item' => $item->id]),
        ]);

        return redirect($checkout_session->url);
    }

    public function success(Item $item)
    {
        $user = Auth::user();

        $order = Order::where('buyer_id', $user->id)
            ->where('item_id', $item->id)
            ->first();

        if ($order) {
            $order->update(['status' => 'completed']);
        }

        return redirect()->route('items.index')
            ->with('success', '購入が完了しました。');
    }

    public function editAddress(Item $item)
    {
        $user = Auth::user();
        return view('user.address', compact('item', 'user'));
    }

    public function updateAddress(AddressRequest $request, Item $item)
    {
        $user = Auth::user();

        $user->profile->update([
            'postcode' => $request->postcode,
            'address'  => $request->address,
            'building' => $request->building,
        ]);

        return redirect()->route('purchase.show', $item->id)
            ->with('success', '配送先住所を更新しました。');
    }
}
