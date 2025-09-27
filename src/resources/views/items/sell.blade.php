@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/sell.css') }}">
@endsection

@section('content')
<div class="sell__content">
    <div class="sell-form__heading">
        <h1 class="sell-form__heading-title">商品の出品</h1>
    </div>

    <form class="sell-form" action="{{ route('items.store') }}" method="post" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="sell-form__section">
            <label for="image" class="sell-form__label">商品画像</label>
            <div class="sell-form__image-wrapper">
                <label for="image" class="sell-form__image-label">画像を選択する</label>
                <input type="file" id="image" name="image" class="sell-form__image-input" hidden>
                <div id="image-preview" class="sell-form__image-preview"></div>
            </div>
            @error('image')
                <p class="sell-form__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="sell-form__section">
            <h2 class="sell-form__section-title">商品の詳細</h2>
            <label class="sell-form__label">カテゴリー</label>
            <div class="sell-form__category-list">
                @foreach ($categories as $category)
                @php $categoryId = 'category_' . $category->id; @endphp
                <div class="sell-form__category-item">
                    <input type="checkbox" id="{{ $categoryId }}" name="category[]" value="{{ $category->id }}" class="sell-form__category-checkbox">
                    <label for="{{ $categoryId }}" class="sell-form__category-name">{{ $category->name }}</label>
                </div>
                @endforeach
            </div>
            @error('category')
                <p class="sell-form__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="sell-form__section">
            <label for="condition" class="sell-form__label">商品の状態</label>
            <select id="condition" name="condition" class="sell-form__select">
                <option value="">選択してください</option>
                @foreach ($conditions as $condition)
                <option value="{{ $condition }}">{{ $condition }}</option>
                @endforeach
            </select>
            @error('condition')
                <p class="sell-form__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="sell-form__section">
            <h2 class="sell-form__section-title">商品名と説明</h2>
            <label for="name" class="sell-form__label">商品名</label>
            <input type="text" id="name" name="name" class="sell-form__input">
            @error('name')
                <p class="sell-form__error">{{ $message }}</p>
            @enderror

            <label for="brand_name" class="sell-form__label">ブランド名</label>
            <input type="text" id="brand_name" name="brand_name" class="sell-form__input">

            <label for="description" class="sell-form__label">商品の説明</label>
            <textarea id="description" name="description" rows="5" class="sell-form__textarea"></textarea>
            @error('description')
                <p class="sell-form__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="sell-form__section">
            <label for="price" class="sell-form__label">販売価格</label>
            <div class="sell-form__price-wrapper">
                <input type="number" id="price" name="price" class="sell-form__price-input">
                <span class="sell-form__yen">&yen;</span>
            </div>
            @error('price')
                <p class="sell-form__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="sell-form__section">
            <button type="submit" class="sell-form__submit-button">出品する</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('image').addEventListener('change', function (event) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';

        const files = event.target.files;
        if (!files.length) return;

        const file = files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = "選択された商品画像のプレビュー";
            preview.appendChild(img);
        };

        reader.readAsDataURL(file);
    });
</script>
@endsection
