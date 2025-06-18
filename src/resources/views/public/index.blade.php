@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/public/index.css') }}">
@endsection

@section('content')
<div class="items-index">
    <div class="index__tabs">
    <a href="{{ route('items.index', ['page' => 'recommend']) }}" class="index__tab {{ $page === 'recommend' ? 'index__tab--active' : '' }}">おすすめ</a>
    <a href="{{ route('items.index', ['page' => 'mylist']) }}" class="index__tab {{ $page === 'mylist' ? 'index__tab--active' : '' }}">マイリスト</a>
    </div>
</div>
<div class="profile__divider"></div>


    <div class="items-index__grid">
        @forelse ($items as $item)
            <a href="{{ route('items.show', ['item' => $item->id]) }}" class="items-index__card">
            <div class="items-index__image-wrapper">
                    <img src="{{ asset('storage/images/' . $item->image) }}" alt="{{ $item->name }}" class="items-index__image">
                </div>
                <div class="items-index__info">
                    <p class="items-index__name">{{ $item->name }}</p>
                    @if ($item->is_sold)
                        <span class="items-index__label items-index__label--sold">Sold</span>
                    @endif
                </div>
            </a>
        @empty
            @if ($page === 'mylist' && !Auth::check())
                <p class="items-index__empty">マイリストを見るにはログインが必要です。</p>
            @else
                <p class="items-index__empty">表示する商品がありません。</p>
            @endif
        @endforelse
    </div>
</div>
@endsection
