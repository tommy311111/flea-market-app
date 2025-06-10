<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        $user = Auth::user();
        $methods = Order::PAYMENT_METHOD;
        return view('items.purchase', compact('item', 'user','methods'));
    }

    /**
     * 商品購入処理（注文登録）
     */
    public function store(PurchaseRequest $request, Item $item)
    {
        $user = Auth::user();

        Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => $request->payment_method,
            'sending_postcode' => $user->profile->postcode,
            'sending_address' => $user->profile->address,
            'sending_building' => $user->profile->building,
        ]);

        // 商品の購入済みフラグなどを更新する場合はここで実装（例: sold フラグ追加）

        return redirect()->route('items.index')->with('success', '購入が完了しました。');
    }

    /**
     * 配送先住所変更画面（PG07）表示
     */
    public function editAddress(Item $item)
    {
        $user = Auth::user();

        return view('user.address', compact('item', 'user'));
    }

    /**
     * 配送先住所の更新処理
     */
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
