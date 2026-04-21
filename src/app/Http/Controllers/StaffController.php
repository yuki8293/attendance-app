<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class StaffController extends Controller
{
    // スタッフ一覧画面を表示するメソッド
    public function list()
    {
        // 全てのユーザー情報を取得
        // → スタッフ一覧に表示するため
        $users = User::all();

        // admin/staff_list.blade.php にデータを渡して表示
        // compact('users') は ['users' => $users] と同じ意味
        return view('admin.staff_list', compact('users'));
    }
}
