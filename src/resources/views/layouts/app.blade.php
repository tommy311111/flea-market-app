<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/app.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__top">
                <p class="header__logo">
                    <a href="{{ route('items.index') }}">
                        <img src="{{ asset('storage/images/logo/logo.svg') }}" alt="COACHTECH">
                    </a>
                </p>

                @if (
                    !in_array(Route::currentRouteName(), ['register.form', 'login', 'verification.notice']) &&
                    !Str::startsWith(Route::currentRouteName(), 'chats.') &&
                    !Str::startsWith(Route::currentRouteName(), 'orders.start')
                )
                    <form action="{{ route('items.index') }}" method="GET" class="header__search-form">
                        <input type="text" name="keyword" class="header__search-input" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
                        <input type="hidden" name="page" value="{{ request('page', 'recommend') }}">
                    </form>
                </div>

                <div class="header__bottom">
                    <nav class="header__nav">
                        <ul class="header__list">
                            <li class="header__list-item">
                                <form action="/logout" class="header__form" method="post">
                                    @csrf
                                    <button class="header__form--logout" type="submit">ログアウト</button>
                                </form>
                            </li>
                            <li class="header__list-item">
                                <a href="{{ route('profile.index') }}" class="header__form--mypage">マイページ</a>
                            </li>
                            <li class="header__list-item">
                                <form action="/sell" class="header__form" method="get">
                                    <button class="header__form--sell">出品</button>
                                </form>
                            </li>
                        </ul>
                    </nav>
                </div>
                @endif
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
