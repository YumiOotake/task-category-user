<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>todos</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <a href="{{ route('tasks.index') }}" class="header__logo-link">Tasks App</a>
            </div>
            <nav class="header__nav">
                @auth
                    <div class="header__nav-item">
                        <a href="{{ route('categories.index') }}" class="header__nav-link">categories</a>
                    </div>
                    <form action="{{ route('logout') }}" method="post" class="header__nav-item">
                        @csrf
                        <button type="submit" class="header__nav-button">ログアウト</button>
                    </form>
                @endauth
                @guest
                    <div class="header__nav-item">
                        <a href="{{ route('login') }}" class="header__nav-link">ログイン</a>
                    </div>
                    <div class="header__nav-item">
                        <a href="{{ route('register') }}" class="header__nav-link">新規登録</a>
                    </div>
                @endguest
            </nav>
        </div>
    </header>
    <main>
        @if (session('success'))
            <div class="category__alert--success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="category__alert--danger">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>
</body>

</html>
