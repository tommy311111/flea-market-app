@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/detail.css') }}">
@endsection

@section('content')
<div class="item-detail">
    <div class="item-detail__container">
        <div class="item-detail__image-wrapper">
            <img src="{{ asset('storage/images/items/' . $item->image) }}" alt="商品画像" class="item-detail__image">
        </div>

        <div class="item-detail__info">
            <h1 class="item-detail__name">{{ $item->name }}</h1>
            <p class="item-detail__brand">{{ $item->brand_name }}</p>
            <p class="item-detail__price">
                ¥{{ number_format($item->price) }} 
                <span class="item-detail__tax">(税込)</span>
            </p>

            <div class="item-detail__icons">
                <div class="item-detail__icon-group">
                    <form action="{{ route('items.like', ['item' => $item->id]) }}" method="POST">
                        @csrf
                        <button type="submit" style="background: none; border: none; padding: 0;">
                            <img
                                src="{{ asset('storage/images/icons/star.png') }}"
                                alt="いいね"
                                class="item-detail__icon {{ $liked ? 'liked' : '' }}">
                        </button>
                        <span class="item-detail__count">{{ $item->likes_count }}</span>
                    </form>
                </div>

                <div class="item-detail__icon-group">
                    <img src="{{ asset('storage/images/icons/comment.png') }}" alt="コメント" class="item-detail__icon">
                    <span class="item-detail__count">{{ $item->comments_count }}</span>
                </div>
            </div>

            <a href="{{ route('purchase.show',['item' => $item->id]) }}" class="item-detail__purchase-button">購入手続きへ</a>

            <div class="item-detail__section">
                <h2 class="item-detail__section-title">商品説明</h2>
                <p class="item-detail__description">{!! nl2br(e($item->description)) !!}</p>
            </div>

            <div class="item-detail__section">
                <h2 class="item-detail__section-title">商品の情報</h2>
                <p class="item-detail__meta">
                    <span class="item-detail__label">カテゴリー</span>
                    <span class="item-detail__tag-list">
                        @foreach($item->categories as $category)
                            <span class="item-detail__tag">{{ $category->name }}</span>
                        @endforeach
                    </span>
                </p>

                <p class="item-detail__meta">
                    <span class="item-detail__label">商品の状態</span>
                    <span class="item-detail__value">{{ $item->condition }}</span>
                </p>
            </div>

            <div class="item-detail__section">
                <h2 class="item-detail__section-title">コメント ({{ $item->comments_count }})</h2>

                @foreach($item->comments as $comment)
                    <div class="item-detail__comment">
                        <div class="item-detail__comment-header">
                            @if ($comment->user->profile && $comment->user->profile->image)
                                <img src="{{ asset('storage/images/profiles/' . $comment->user->profile->image) }}" alt="ユーザー画像" class="item-detail__comment-user-image">
                            @else
                                <div class="item-detail__comment-user-image--placeholder"></div>
                            @endif
                            <span class="item-detail__comment-username">{{ $comment->user->name }}</span>
                        </div>
                        <p class="item-detail__comment-text">{!! nl2br(e($comment->body)) !!}</p>
                    </div>
                @endforeach
            </div>

            <div class="item-detail__section">
                <h2 class="item-detail__section-title">商品へのコメント</h2>
                <form action="{{ route('comment.store', $item->id) }}" method="POST" class="item-detail__comment-form" novalidate>
                    @csrf
                    <textarea name="body" class="item-detail__textarea" maxlength="255" required></textarea>
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