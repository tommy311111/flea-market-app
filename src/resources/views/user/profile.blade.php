@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/profiles.css') }}">
@endsection

@section('content')
<div class="profile__content">

    {{-- --- プロフィール上部 --- --}}
    <div class="profile__top">
        <div class="profile__image-wrapper">
            @if ($profile->image)
                <img src="{{ asset('storage/images/profiles/' . $profile->image) }}" alt="プロフィール画像" class="profile__image">
            @else
                <div class="profile__image--placeholder"></div>
            @endif
        </div>

        <div class="profile__info-group">
            <div class="profile__name">{{ $user->name }}</div>
            @if ($user->average_rating)
                <p class="profile__rating">
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="{{ $i <= $user->average_rating ? 'star-filled' : 'star-empty' }}">★</span>
                    @endfor
                </p>
            @endif
        </div>

        <div class="profile__edit-wrapper">
            <a href="{{ route('profile.edit') }}" class="profile__edit-button">プロフィールを編集</a>
        </div>
    </div>

    {{-- --- タブ --- --}}
    <div class="profile__tabs">
        <a href="{{ route('profile.index', ['page' => 'sell']) }}" class="profile__tab {{ $page === 'sell' ? 'profile__tab--active' : '' }}">
            出品した商品
        </a>
        <a href="{{ route('profile.index', ['page' => 'buy']) }}" class="profile__tab {{ $page === 'buy' ? 'profile__tab--active' : '' }}">
            購入した商品
        </a>
        <a href="{{ route('profile.index', ['page' => 'transaction']) }}" class="profile__tab {{ $page === 'transaction' ? 'profile__tab--active' : '' }}">
            取引中の商品
            @if (!empty($total_new_messages) && $total_new_messages > 0)
                <span class="profile__tab-badge">{{ $total_new_messages }}</span>
            @endif
        </a>
    </div>

    {{-- --- タブごとの商品リスト --- --}}
    <div class="items-index__grid">
        @forelse ($items as $item)
            @php
                // 関連する注文を取得（ある場合のみ）
                $order = $orders->firstWhere('item_id', $item->id);
                $new_count = $order->new_messages_count ?? 0;
            @endphp

            {{-- 取引中・完了タブはチャットルームへのリンク、それ以外は商品詳細 --}}
            @if (in_array($page, ['transaction', 'completed']) && $order)
                <a href="{{ route('chats.show', $order->id) }}" class="items-index__card">
            @else
                <a href="{{ route('items.show', $item->id) }}" class="items-index__card">
            @endif

                {{-- 新着メッセージバッジ --}}
                @if ($new_count > 0)
                    <span class="items-index__badge">{{ $new_count }}</span>
                @endif

                <div class="items-index__image-wrapper">
                    <img src="{{ asset('storage/images/items/' . $item->image) }}" alt="{{ $item->name }}" class="items-index__image">
                </div>
                <div class="items-index__info">
                    <p class="items-index__name">{{ $item->name }}</p>

                    {{-- Soldラベル表示 --}}
                    @if ($order && in_array($order->status, ['in_progress', 'completed']))
                        <span class="items-index__label items-index__label--sold">Sold</span>
                    @endif
                </div>
            </a>
        @empty
            <p class="items-index__empty">表示する商品がありません</p>
        @endforelse
    </div>
</div>
@endsection
