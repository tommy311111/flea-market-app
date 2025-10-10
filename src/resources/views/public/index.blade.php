@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/public/index.css') }}">
@endsection

@section('content')
<div class="items-index">
    <div class="index__tabs">
        <a href="{{ route('items.index', ['page' => 'recommend', 'keyword' => request('keyword')]) }}"
           class="index__tab {{ $page === 'recommend' ? 'index__tab--active' : '' }}">
            おすすめ
        </a>
        <a href="{{ route('items.index', ['page' => 'mylist', 'keyword' => request('keyword')]) }}"
           class="index__tab {{ $page === 'mylist' ? 'index__tab--active' : '' }}">
            マイリスト
        </a>
    </div>
</div>

<div class="profile__divider"></div>

<div class="items-index__grid">
    @forelse ($items as $item)
        <a href="{{ route('items.show', ['item' => $item->id]) }}" class="items-index__card">
            <div class="items-index__image-wrapper">
                <img src="{{ asset('storage/images/items/' . $item->image) }}" alt="{{ $item->name }}" class="items-index__image">
            </div>
            <div class="items-index__info">
                <p class="items-index__name">{{ $item->name }}</p>
                @php
                    $order = $orders->firstWhere('item_id', $item->id);
                @endphp
                {{-- Soldラベル表示 --}}
                @if ($order && in_array($order->status, ['in_progress', 'completed']))
                    <span class="items-index__label items-index__label--sold">Sold</span>
                @endif
            </div>
        </a>
    @empty
        <p class="items-index__empty">
            @if ($page === 'mylist' && !Auth::check())
                マイリストを見るにはログインが必要です
            @elseif ($keyword)
                @if ($page === 'mylist')
                    “{{ $keyword }}” に一致するマイリストの商品は見つかりませんでした
                @else
                    “{{ $keyword }}” に一致する商品は見つかりませんでした
                @endif
            @else
                @if ($page === 'mylist')
                    マイリストに登録された商品はまだありません
                @else
                    現在、他のユーザーによる出品はありません
                @endif
            @endif
        </p>
    @endforelse
</div>
@endsection
