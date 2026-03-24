<?php

namespace App\Http\Controllers;
// ログインユーザーの情報をとるための物
use Illuminate\Support\Facades\Auth;
// 時間を扱う便利ツール
use Carbon\Carbon;
use App\Models\Attendance;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    //画面表示
    public function index()
    {
        return view('attendance.index');
    }

    // どのボタンが押されたかで処理を分ける
    //処理
    public function store(Request $request)
    {
        // 出勤ボタンが押された場合、開始時間を保存する
        if ($request->action === 'start') {
            // 出勤処理(データ作る)
            // 今日の日付
            $today = Carbon::today();

            // データ作成（出勤）
            Attendance::create([
                'user_id' => Auth::id(),
                'work_date' => $today,
                'start_time' => Carbon::now()->format('H:i:s'),
            ]);
        } elseif ($request->action === 'end') {
            // 退勤処理（更新）
        }

        // 勤怠画面にリダイレクト
        return redirect('/attendance');
    }
}
