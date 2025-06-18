@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/purchase.css') }}">
@endsection

@section('content')
<div class="purchase__content">
        <div class="purchase__main">
            <div class="purchase__left">
                <!-- 商品情報 -->
                <div class="purchase__section">
                    <div class="purchase__item-info">
                        <img src="{{ asset('storage/images/' . $item->image) }}" alt="{{ $item->name }}" class="purchase__item-image">
                        <div class="purchase__item-text">
                            <div class="purchase__item-name">{{ $item->name }}</div>
                            <div class="purchase__item-price">¥{{ number_format($item->price) }}</div>
                        </div>
                    </div>
                </div>

                <hr class="purchase__divider">

        <div class="purchase__section">
                <!-- 支払い方法 -->
    <form action="{{ route('purchase.savePaymentMethod', $item->id) }}" method="POST">
                @csrf
                    <label for="payment_method" class="purchase__label">支払い方法</label>
                <div class="purchase__select-inner">
                    <select name="payment_method" id="payment_method" class="purchase__select"  onchange="this.form.submit()">
                        <option value="">選択してください</option>
                        @foreach ($methods as $method)
        <option value="{{ $method }}">{{ $method }}</option>
    @endforeach
                    </select>
                </div>
                    @error('payment_method')
                        <div class="purchase__error">{{ $message }}</div>
                    @enderror
                 </form>   
                </div>
        <hr class="purchase__divider">
        
                <!-- 配送先 -->
                <div class="purchase__section">
                    <div class="purchase__address-header">
                        <span class="purchase__label">配送先</span>
                        <a href="{{ route('purchase.address.edit', $item->id) }}" class="purchase__edit-button">変更する</a>
                    </div>
                    <div class="purchase__address">
                    〒{{ $user->profile->postcode }}<br>
                        {{ $user->profile->address }}<br>
                        {{ $user->profile->building }}
                        <input type="hidden" name="shipping_address" value="{{ '〒' .$user->profile->postcode . ' ' . $user->profile->address . ' ' . $user->profile->building }}">
                    </div>
                    @error('shipping_address')
                        <div class="purchase__error">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="purchase__divider">

            </div>

            <div class="purchase__right">
    <form action="{{ route('purchase.store', $item->id) }}" method="POST">
        @csrf
        <input type="hidden" name="shipping_address" value="{{ '〒' . $user->profile->postcode . ' ' . $user->profile->address . ' ' . $user->profile->building }}">
            <div class="purchase__summary">
    <table class="purchase__summary-table">
        <tbody>
            <tr>
                <th>商品代金</th>
                <td>¥{{ number_format($item->price) }}</td>
            </tr>
            <tr>
                <th>支払い方法</th>
                <td id="selected-payment-method">{{ $selectedPayment ?? '未選択' }}</td>
            </tr>
        </tbody>
    </table>
</div>

                <button type="submit" class="purchase__submit-button">購入する</button>
                </form>
            </div>
        </div>
</div>


@endsection
