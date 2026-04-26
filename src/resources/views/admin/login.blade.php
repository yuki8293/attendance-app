@extends('layouts.admin')

{{-- ここは管理者ログイン画面 --}}
@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')

<div class="login-form__content">
    <div class="login-form__heading">
        <h1>管理者ログイン</h1>
    </div>

    <form method="POST" action="{{ route('admin.login') }}" class="form">
        @csrf

        <div class="form__group">
            <div class="form__group-title">
                <label>メールアドレス</label>
            </div>
            <div class="form__input--text">
                <input type="email" name="email" value="{{ old('email') }}">
            </div>
            {{-- エラーメッセージ --}}
            @error('email')
            <div class="form__error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form__group">
            <div class="form__group-title">
                <label>パスワード</label>
            </div>
            <div class="form__input--text">
                <input type="password" name="password">
            </div>

            {{-- エラーメッセージ --}}
            @error('password')
            <div class="form__error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form__button">
            <button class="form__button-submit">管理者ログインする</button>
        </div>
    </form>
</div>


@endsection