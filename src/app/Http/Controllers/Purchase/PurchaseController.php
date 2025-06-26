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
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $order->payment_method = $request->payment_method;
        $order->sending_postcode = $user->profile->postcode;
        $order->sending_address = $user->profile->address;
        $order->sending_building = $user->profile->building;
        $order->save();

        return redirect()->route('purchase.show', $item->id)
                         ->with('selectedPayment', $request->payment_method);
    }

    public function show(Item $item)
    {
        $user = Auth::user();
        $methods = Order::PAYMENT_METHOD;
        $selectedPayment = session('selectedPayment');

        if (!$selectedPayment) {
            $order = Order::where('user_id', $user->id)
                          ->where('item_id', $item->id)
                          ->first();
            $selectedPayment = $order->payment_method ?? null;
        }

        return view('items.purchase', compact('item', 'user', 'methods', 'selectedPayment'));
    }

    public function store(PurchaseRequest $request, Item $item)
    {
        $user = Auth::user();

        $order = Order::firstOrNew([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $order->payment_method = $request->payment_method;
        $order->sending_postcode = $user->profile->postcode;
        $order->sending_address = $user->profile->address;
        $order->sending_building = $user->profile->building;
        $order->save();

        return redirect()->route('items.index')->with('success', '購入が完了しました。');
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
            'address' => $request->address,
            'building' => $request->building,
        ]);

        return redirect()->route('purchase.show', $item->id)->with('success', '配送先住所を更新しました。');
    }
}
