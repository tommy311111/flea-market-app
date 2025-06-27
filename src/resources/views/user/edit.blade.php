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
        @method('PUT')

        <div class="form__group profile-image__group">
            <div id="image-preview" class="profile__image-wrapper">
                @if ($profile && $profile->image)
                    <img src="{{ asset('storage/images/profiles/' . $profile->image) }}" alt="プロフィール画像" class="profile__image">
                @else
                    <div class="profile__image--placeholder"></div>
                @endif
            </div>
            <label class="profile-image__button">
                画像を選択する
                <input type="file" name="image" id="image" hidden>
            </label>
        </div>
        <div class="form__error">
            @error('image')
                {{ $message }}
            @enderror
        </div>

        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">ユーザー名</span>
            </div>
            <div class="form__input--text">
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form__input--text-field">
            </div>
            <div class="form__error">
                @error('name')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">郵便番号</span>
            </div>
            <div class="form__input--text">
                <input type="text" name="postcode" value="{{ old('postcode', $profile->postcode) }}" class="form__input--text-field">
            </div>
            <div class="form__error">
                @error('postcode')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">住所</span>
            </div>
            <div class="form__input--text">
                <input type="text" name="address" value="{{ old('address', $profile->address) }}" class="form__input--text-field">
            </div>
            <div class="form__error">
                @error('address')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">建物名</span>
            </div>
            <div class="form__input--text">
                <input type="text" name="building" value="{{ old('building', $profile->building) }}" class="form__input--text-field">
            </div>
            <div class="form__error">
                @error('building')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>

<script>
    document.querySelector('input[name="image"]').addEventListener('change', function (event) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';

        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'profile__image';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
</script>
@endsection
