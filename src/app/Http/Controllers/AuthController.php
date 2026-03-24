<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Authという機能をこのファイルで使えるようにする

class AuthController extends Controller
{
    // トップページ（今は使ってない）
    public function index()
    {
        return view('index');
    }

    // /login にアクセスしたらログイン画面を表示する
    public function showLoginForm()
    {
        // login.blade.php を表示
        return view('auth.login');
    }

    // /registerにアクセスしたら会員登録画面を表示する
    public function showRegisterForm()
    {
        // register.blade.phpを表示
        return view('auth.register');
    }

    // ログイン処理
    public function login(Request $request)
    {
        // 入力されたデータの中から「メール」と「パスワード」を取り出してログインに使う
        $loginData = $request->only('email', 'password');

        // ログインできるかチェック
        if (Auth::attempt($loginData)) {
            return redirect('/attendance');
        }
        // 失敗したらエラーメッセージ
        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);
    }
}
