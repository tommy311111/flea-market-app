@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/purchase.css') }}">
@endsection

@section('content')
<div class="purchase__content">
    <form action="{{ route('purchase.store', $item->id) }}" method="POST">
        @csrf
        <div class="purchase__main">
            <div class="purchase__left">
                <!-- 商品情報 -->
                <div class="purchase__section">
                    <div class="purchase__item-info">
                        <img src="{{ asset('storage/images/' . $item->image) }}" alt="{{ $item->name }}" class="purchase__item-image">
                        <div>
                            <div class="purchase__item-name">{{ $item->name }}</div>
                            <div class="purchase__item-price">¥{{ number_format($item->price) }}</div>
                        </div>
                    </div>
                </div>

                <hr class="purchase__divider">

                <!-- 支払い方法 -->
                <div class="purchase__section">
                    <label for="payment_method" class="purchase__label">支払い方法</label>
                    <select name="payment_method" id="payment_method" class="purchase__select">
                        <option value="">選択してください</option>
                        @foreach ($methods as $method)
        <option value="{{ $method }}">{{ $method }}</option>
    @endforeach
                    </select>
                    @error('payment_method')
                        <div class="purchase__error">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="purchase__divider">

                <!-- 配送先 -->
                <div class="purchase__section">
                    <div class="purchase__address-header">
                        <span class="purchase__label">配送先</span>
                        <a href="{{ route('purchase.address.edit', $item->id) }}" class="purchase__edit-button">変更する</a>
                    </div>
                    <div class="purchase__address">
                        {{ $user->profile->postcode }}<br>
                        {{ $user->profile->address }}<br>
                        {{ $user->profile->building }}
                        <input type="hidden" name="shipping_address" value="{{ $user->profile->postcode . ' ' . $user->profile->address . ' ' . $user->profile->building }}">
                    </div>
                    @error('shipping_address')
                        <div class="purchase__error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="purchase__right">
                <div class="purchase__summary">
                    <div class="purchase__summary-row">
                        <span>商品代金</span>
                        <span>¥{{ number_format($item->price) }}</span>
                    </div>
                    <div class="purchase__summary-row">
                        <span>支払い方法</span>
                        <span id="selected-payment-method">{{ old('payment_method') }}</span>
                    </div>
                </div>
                <button type="submit" class="purchase__submit-button">購入する</button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectElement = document.getElementById('payment_method');
        const displayElement = document.getElementById('selected-payment-method');

        selectElement.addEventListener('change', function () {
            displayElement.textContent = this.value || '—';
        });

        // ページ読み込み時に old() の値があれば初期反映
        if (selectElement.value) {
            displayElement.textContent = selectElement.value;
        }
    });
</script>

@endsection
