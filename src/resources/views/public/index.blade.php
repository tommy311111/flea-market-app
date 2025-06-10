@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/public/index.css') }}">
@endsection

@section('content')
<div class="items-index">
    <div class="items-index__header">
        <h2 class="items-index__tab {{ $tab === 'recommend' ? 'items-index__tab--active' : '' }}">
            <a href="{{ url('/items?tab=recommend') }}" class="items-index__link">おすすめ</a>
        </h2>
        <h2 class="items-index__tab {{ $tab === 'mylist' ? 'items-index__tab--active' : '' }}">
            <a href="{{ url('/items?tab=mylist') }}" class="items-index__link">マイリスト</a>
        </h2>
    </div>
    <hr class="items-index__divider">

    <div class="items-index__grid">
        @forelse ($items as $item)
            <a href="{{ url('/items/' . $item->id) }}" class="items-index__card">
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
            @if ($tab === 'mylist' && !Auth::check())
                <p class="items-index__empty">マイリストを見るにはログインが必要です。</p>
            @else
                <p class="items-index__empty">表示する商品がありません。</p>
            @endif
        @endforelse
    </div>
</div>
@endsection
