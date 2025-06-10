@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/edit.css') }}">
@endsection

@section('content')
<div class="profile__content">
    <div class="profile-form__heading">
        <h2 class="profile-form__heading-title">プロフィール設定</h2>
    </div>
    <form class="profile-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @method('POST')

        <!-- プロフィール画像 -->
        <div class="form__group profile-image__group">
            <div class="profile-image__preview">
                <img src="{{ asset('storage/' . $profile->image) }}" alt="プロフィール画像">
            </div>
            <label class="profile-image__button">
                画像を選択する
                <input type="file" name="image" hidden>
            </label>
        </div>

        <!-- ユーザー名 -->
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">ユーザー名</span>
            </div>
            <div class="form__input--text">
                <input type="text" name="name" value="{{ old('name', $user->name) }}">
            </div>
            <div class="form__error">@error('name') {{ $message }} @enderror</div>
        </div>

        <!-- 郵便番号 -->
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">郵便番号</span>
            </div>
            <div class="form__input--text">
                <input type="text" name="postcode" value="{{ old('postcode', $user->postcode) }}">
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
                <span class="form__label--item">住所</span>
            </div>
            <div class="form__input--text">
                <input type="text" name="address" value="{{ old('address', $user->address) }}">
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
                <span class="form__label--item">建物名</span>
            </div>
            <div class="form__input--text">
                <input type="text" name="building" value="{{ old('building', $user->building) }}">
                <div class="form__error">
                    @error('building')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <!-- 送信ボタン -->
        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection
