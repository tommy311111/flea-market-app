@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/profiles.css') }}">
@endsection

@section('content')
<div class="profile__content">
<div class="profile__top">
    <!-- 左側 -->
    
        <div class="profile__image-wrapper">
            @if ($profile->image)
                <img src="{{ asset('storage/images/profiles/' . $profile->image) }}" alt="プロフィール画像" class="profile__image">
            @else
                <div class="profile__image--placeholder"></div>
            @endif
        </div>

        <div class="profile__info-group">
            <h2 class="profile__name">{{ $user->name }}</h2>
        </div>
    

    <!-- 右側 -->
    <div class="profile__edit-wrapper">
        <a href="{{ route('profile.edit') }}" class="profile__edit-button">プロフィールを編集</a>
    </div>
</div>
 <!-- ← profile__top の閉じタグ -->

    <div class="profile__tabs">
        <a href="{{ route('profile.index', ['page' => 'sell']) }}" class="profile__tab {{ $page === 'sell' ? 'profile__tab--active' : '' }}">出品した商品</a>
        <a href="{{ route('profile.index', ['page' => 'buy']) }}" class="profile__tab {{ $page === 'buy' ? 'profile__tab--active' : '' }}">購入した商品</a>
    </div>

    <div class="items-index__grid">
        @forelse ($items as $item)
            <a href="{{ url('/items/' . $item->id) }}" class="items-index__card">
                <div class="items-index__image-wrapper">
                    <img src="{{ asset('storage/images/items/' . $item->image) }}" alt="{{ $item->name }}" class="items-index__image">
                </div>
                <div class="items-index__info">
                    <p class="items-index__name">{{ $item->name }}</p>
                    @if ($item->is_sold)
                        <span class="items-index__label items-index__label--sold">Sold</span>
                    @endif
                </div>
            </a>
        @empty
            <p class="items-index__empty">表示する商品がありません</p>
        @endforelse
    </div>
</div> <!-- ← profile__content の閉じタグ -->
@endsection
