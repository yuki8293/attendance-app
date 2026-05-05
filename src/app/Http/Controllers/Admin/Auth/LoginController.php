<?php
// ここは管理者用のコントローラ

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminLoginRequest;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // 管理者ログイン画面を表示する
        return view('admin.login');
    }

    // 管理者のログイン処理
    public function login(AdminLoginRequest $request)
    {
        // 管理者用guardを使ってログイン認証を行う
        // adminテーブルのEメールとパスワードをチェック
        if (Auth::guard('admin')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            // ログイン成功時、管理者退勤一覧画面へリダイレクト
            return redirect()->route('admin.attendance.list');
        }

        // ログイン失敗時、エラーメッセージを表示してログイン画面に戻る
        return back()->withErrors([
            'login_error' => 'ログイン情報が登録されていません。'
        ])->withInput();
    }

    public function logout()
    {
        Auth::guard('admin')->logout();

        return redirect()->route('admin.login');
    }
}
