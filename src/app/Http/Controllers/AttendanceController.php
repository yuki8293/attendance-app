<?php
// ここは、勤怠登録画面のコントローラ
namespace App\Http\Controllers;
// ログインユーザーの情報をとるための物
use Illuminate\Support\Facades\Auth;
// 時間を扱う便利ツール
use Carbon\Carbon;
use App\Models\Attendance;

use Illuminate\Http\Request;
use App\Models\BreakTime;

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
        // 今日の勤怠データ取得
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('work_date', Carbon::today())
            ->first();

        // 出勤
        if ($request->action === 'start') {
            Attendance::create([
                'user_id' => Auth::id(),
                'work_date' => Carbon::today(),
                'start_time' => Carbon::now(),
            ]);
        }

        // 休憩開始
        elseif ($request->action === 'break_start' && $attendance) {
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'start_time' => now(), // ← ここ重要
            ]);
        }

        // 休憩終了
        elseif ($request->action === 'break_end' && $attendance) {
            $break = BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('end_time')
                ->latest()
                ->first();

            if ($break) {
                $break->update([
                    'end_time' => now(),
                ]);
            }
        }

        // 退勤
        elseif ($request->action === 'end' && $attendance) {
            $attendance->update([
                'end_time' => Carbon::now()->format('H:i:s'),
            ]);
        }

        return redirect()->route('attendance.index');
    }
}
