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
            ->latest()
            ->first();

        // 出勤
        if ($request->action === 'start') {

            if ($attendance) {
                return redirect()->back()->with('error', '本日はすでに出勤しています');
            }

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

            $onBreak = BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('end_time')
                ->exists();

            if ($onBreak) {
                return redirect()->back()->with('error', '休憩中は退勤できません');
            }

            $attendance->update([
                'end_time' => Carbon::now(),
            ]);
        }

        return redirect()->route('attendance.index');
    }

    public function list(Request $request)
    {
        // ログインしているユーザーのIDを取得
        $userId = Auth::id();

        // クエリパラメータから月を取得（なければ今月）
        $month = $request->input('month', now()->format('Y-m'));

        // 月の開始・終了を作成
        $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endOfMonth = Carbon::createFromFormat('Y-m', $month)->endOfMonth();


        // 自分のデータのみ
        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth]) // この期間のデータだけ取得する

            ->orderBy('work_date', 'desc') // 並び順OK
            ->get(); // 複数取得OK

        // 一覧画面にデータを渡す
        return view('attendance.list', compact('attendances', 'month'));
    }

    public function detail($id)
    {
        // 「勤怠データ」と「関連する休憩データ」をまとめて取得する
        $attendance = Attendance::with('breaks')->findOrFail($id);

        // 取得した勤怠データを詳細画面に渡す
        return view('attendance.detail', compact('attendance'));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        // 出勤・退勤の更新
        $attendance->update([
            'start_time' => $request->clock_in,
            'end_time'   => $request->clock_out,
            'note'       => $request->note,
        ]);

        // 休憩（簡易版：1件目）
        if (isset($attendance->breaks[0])) {
            $attendance->breaks[0]->update([
                'start_time' => $request->break1_start,
                'end_time'   => $request->break1_end,
            ]);
        }

        return redirect()->route('attendance.detail', $id);
    }
}
