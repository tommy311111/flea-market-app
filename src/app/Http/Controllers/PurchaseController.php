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
    public function savePaymentMethod(Request $request, Item $item)
{
    $request->validate([
        'payment_method' => 'required|in:コンビニ払い,カード支払い',
    ]);
    
    

    $user = Auth::user();

    // 既存の注文があれば更新、なければ新規作成
    $order = Order::firstOrNew([
        'user_id' => $user->id,
        'item_id' => $item->id,
    ]);

    $order->payment_method = $request->payment_method;

    // 住所情報もセット（null許容なら省略可能）
    $order->sending_postcode = $user->profile->postcode;
    $order->sending_address = $user->profile->address;
    $order->sending_building = $user->profile->building;

    $order->save();

    // 保存後、同じ購入ページにリダイレクトして、選択値を表示させる
    return redirect()->route('purchase.show', $item->id)->with('selectedPayment', $request->payment_method);
}


    public function show(Item $item)
    {
        $user = Auth::user();
        $methods = Order::PAYMENT_METHOD;

        // セッションから取得。なければOrdersテーブルの値を取得する方法もあり
    $selectedPayment = session('selectedPayment');

    // または、既存注文の支払い方法を取得しても良い
    if (!$selectedPayment) {
        $order = Order::where('user_id', $user->id)->where('item_id', $item->id)->first();
        $selectedPayment = $order->payment_method ?? null;
    }

        return view('items.purchase', compact('item', 'user','methods', 'selectedPayment'));
    }

    /**
     * 商品購入処理（注文登録）
     */
    public function store(PurchaseRequest $request, Item $item)
{
    $user = Auth::user();

    // 注文がすでにあるか確認（支払い方法だけ更新など）
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
