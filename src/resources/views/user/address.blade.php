@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/address.css') }}">
@endsection

@section('content')
<div class="address__content">
    <div class="address-form__heading">
        <h1 class="address-form__heading-title">住所の変更</h1>
    </div>
    <form action="{{ route('purchase.address.update', ['item' => $item->id]) }}" method="POST">
        @csrf

        <!-- 郵便番号 -->
        <div class="form__group">
            <div class="form__group-title">
                <label for="postcode" class="form__label--item">郵便番号</label>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" id="postcode" name="postcode" value="{{ old('postcode', $user->profile->postcode ?? '') }}">
                </div>
                <div class="form__error">
                    @error('postcode')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <!-- 住所 -->
        <div class="form__group">
            <div class="form__group-title">
                <label for="address" class="form__label--item">住所</label>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" id="address" name="address" value="{{ old('address', $user->profile->address ?? '') }}">
                </div>
                <div class="form__error">
                    @error('address')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <!-- 建物名 -->
        <div class="form__group">
            <div class="form__group-title">
                <label for="building" class="form__label--item">建物名</label>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" id="building" name="building" value="{{ old('building', $user->profile->building ?? '') }}">
                </div>
                <div class="form__error">
                    @error('building')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">変更する</button>
        </div>
    </form>
</div>
@endsection
