<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="/">
                    <img src="{{ asset('images/coachtech-logo.png') }}" alt="coachtechロゴ">
                </a>

                <nav>
                    <ul class="header-nav">
                        @if (Auth::guard('admin')->check())

                        <li class="header-nav__item">
                            <a class="header-nav__link" href="{{ route('admin.attendance.list') }}">勤怠一覧</a>
                        </li>

                        <li class="header-nav__item">
                            <a class="header-nav__link" href="{{ route('admin.staff.list') }}">スタッフ一覧</a>
                        </li>

                        <li class="header-nav__item">
                            <a class="header-nav__link" href="{{ route('admin.stamp_request.list') }}">申請一覧</a>
                        </li>

                        <li class="header-nav__item">
                            <form class="logout-form" action="{{ route('admin.logout') }}" method="post">
                                @csrf
                                <button class="header-nav__button">ログアウト</button>
                            </form>
                        </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>