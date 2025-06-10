@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/detail.css') }}">
@endsection

@section('content')
<div class="item-detail">

    <div class="item-detail__container">
        {{-- 左：商品画像 --}}
        <div class="item-detail__image-wrapper">
            <img src="{{ asset('storage/images/' . $item->image) }}" alt="商品画像" class="item-detail__image">

        </div>

        {{-- 右：商品情報 --}}
        <div class="item-detail__info">

            <h1 class="item-detail__name">{{ $item->name }}</h1>

            <p class="item-detail__brand">{{ $item->brand }}</p>

            <p class="item-detail__price">¥{{ number_format($item->price) }} <span class="item-detail__tax">(税込み)</span></p>

            <div class="item-detail__icons">

@php
    $likedItems = explode(',', request()->cookie('liked_items', ''));
    $isLiked = in_array($item->id, $likedItems);
@endphp
    <div class="item-detail__icon-group">
        <!-- いいね（星）アイコン -->
        <img src="{{ asset('storage/images/icons/star.png') }}" alt="いいね" class="item-detail__icon {{ $isLiked ? 'liked' : '' }}">
        <span class="item-detail__count">{{ $item->likes_count }}</span>
    </div>
    <div class="item-detail__icon-group">
        <!-- コメント（吹き出し）アイコン -->
        <img src="{{ asset('storage/images/icons/comment.png') }}" alt="コメント" class="item-detail__icon">
        <span class="item-detail__count">{{ $item->comments_count }}</span>
    </div>
</div>


            <a href="{{ route('purchase.show',['item' => $item->id]) }}" class="item-detail__purchase-button">購入手続きへ</a>

            <div class="item-detail__section">
                <h2 class="item-detail__section-title">商品説明</h2>
                <p class="item-detail__description">{{ $item->description }}</p>
            </div>

            <div class="item-detail__section">
                <h2 class="item-detail__section-title">商品の情報</h2>
                <p class="item-detail__meta">カテゴリー: {{ implode(', ', $item->categories->pluck('name')->toArray()) }}</p>
                <p class="item-detail__meta">商品の状態: {{ $item->condition }}</p>
            </div>

            <div class="item-detail__section">
                <h2 class="item-detail__section-title">コメント ({{ $item->comments_count }})</h2>

                @foreach($item->comments as $comment)
                    <div class="item-detail__comment">
                        <div class="item-detail__comment-header">
                        <img src="{{ asset('storage/' . $comment->user->profile->image) }}" alt="ユーザー画像" class="item-detail__comment-user-image">

                            <span class="item-detail__comment-username">{{ $comment->user->name }}</span>
                        </div>
                        <p class="item-detail__comment-text">{{ $comment->body }}</p>
                    </div>
                @endforeach
            </div>

            
            <div class="item-detail__section">
                <h2 class="item-detail__section-title">商品へのコメント</h2>
            <form action="{{ route('comment.store', $item->id) }}" method="POST" class="item-detail__comment-form" novalidate>
                    @csrf
                    <textarea name="body" class="item-detail__textarea" placeholder="コメントを入力してください" maxlength="255" required></textarea>
                    @error('body')
        <div class="item__error">{{ $message }}</div>
                    @enderror
                    <button type="submit" class="item-detail__submit-button">コメントを送信する</button>
                </form>
            </div>
            

        </div>
    </div>

</div>
@endsection
