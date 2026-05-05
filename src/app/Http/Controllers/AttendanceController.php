<?php
// ここは、勤怠登録画面のコントローラ
namespace App\Http\Controllers;
// ログインユーザーの情報をとるための物
use Illuminate\Support\Facades\Auth;
// 時間を扱う便利ツール
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\AttendanceRequest;

use Illuminate\Http\Request;
use App\Models\BreakTime;
use App\Models\User;

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

        $request->validate([
            'clock_in' => 'required',
            'clock_out' => 'required|after:clock_in',
            'note' => 'required',
        ], [
            'clock_out.after' => '出勤時間が不適切な値です',
        ]);

        if ($request->has('breaks')) {
            foreach ($request->breaks as $break) {

                // 休憩開始が退勤より後
                if (!empty($break['start']) && $break['start'] > $request->clock_out) {
                    return back()
                        ->withErrors(['break_start' => '休憩時間が不適切な値です'])
                        ->withInput();
                }

                // 休憩終了が退勤より後
                if (!empty($break['end']) && $break['end'] > $request->clock_out) {
                    return back()
                        ->withErrors(['break_end' => '休憩時間もしくは退勤時間が不適切な値です'])
                        ->withInput();
                }
            }
        }

        // 勤怠データ取得（休憩も一緒に）
        $attendance = Attendance::with('breaks')->findOrFail($id);

        // 出勤・退勤・備考を更新
        $attendance->update([

            'start_time' => $request->clock_in
                ? Carbon::parse($attendance->work_date)->format('Y-m-d') . ' ' . $request->clock_in
                : null,

            'end_time' => $request->clock_out
                ? Carbon::parse($attendance->work_date)->format('Y-m-d') . ' ' . $request->clock_out
                : null,

            'note'       => $request->note,
        ]);

        // 休憩データが送信されている場合
        if ($request->has('breaks')) {

            // 既存の休憩データをループで処理
            foreach ($request->breaks as $index => $breakData) {

                $break = $attendance->breaks[$index] ?? null;

                if ($break) {
                    $break->update([
                        'start_time' => $breakData['start'],
                        'end_time'   => $breakData['end'],
                    ]);
                }
            }
        }

        // 新しく追加された休憩が入力されている場合のみ登録
        if ($request->break_new_start && $request->break_new_end) {
            BreakTime::create([

                // どの勤怠に紐づく休憩か
                'attendance_id' => $attendance->id,

                // 新しい休憩の開始時間
                'start_time'    => $request->break_new_start,

                // 新しい休憩の終了時間
                'end_time'      => $request->break_new_end,
            ]);
        }

        //修正申請を作成
        AttendanceRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => auth()->id(),
            'start_time' => $request->clock_in,
            'end_time' => $request->clock_out,
            'note' => $request->note,
            'status' => '承認待ち',
        ]);

        // 更新後、詳細画面にリダイレクト
        return redirect()->route('attendance.pending', $id);
    }

    public function adminUpdate(Request $request, $id)
    {

        // 出勤・退勤チェック
        if ($request->start_time && $request->end_time && $request->start_time >= $request->end_time) {
            return back()->withErrors([
                'start_time' => '出勤時間もしくは退勤時間が不適切な値です'
            ]);
        }

        // 休憩開始 > 退勤
        if ($request->break_start && $request->end_time && $request->break_start > $request->end_time) {
            return back()->withErrors([
                'break_start' => '休憩時間が不適切な値です'
            ]);
        }

        // 休憩終了 > 退勤
        if ($request->break_end && $request->end_time && $request->break_end > $request->end_time) {
            return back()->withErrors([
                'break_end' => '休憩時間もしくは退勤時間が不適切な値です'
            ]);
        }

        // 備考チェック
        if (empty($request->note)) {
            return back()->withErrors([
                'note' => '備考を記入してください'
            ]);
        }

        // 更新
        Attendance::findOrFail($id)->update([
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'note'       => $request->note,
        ]);

        return redirect()->route('admin.attendance.detail', $id);
    }

    // 承認待ち画面を表示するメソッド
    public function pending($id)
    {
        // 指定されたIDの勤怠データを取得
        // 休憩（breaks）とユーザー情報（user）も一緒に取得する
        $attendance = Attendance::with('breaks', 'user')->findOrFail($id);

        // 指定された勤怠IDに紐づく「承認待ち」の修正申請を取得
        // 最新の申請を1件だけ取得する
        $requestData = AttendanceRequest::where('attendance_id', $id)
            ->where('status', '承認待ち')
            ->latest()
            ->first();

        // 承認待ち画面（pending.blade.php）を表示
        // $attendance のデータをビューに渡す
        return view('attendance.pending', compact('attendance', 'requestData'));
    }

    // 管理者：勤怠一覧
    public function adminList(Request $request)
    {

        // URLに日付があればそれを使う、なければ今日を使う
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))
            : now();

        // 時間を0時にそろえて「その日単位」で扱えるようにする
        $date = $date->startOfDay();

        // その日の勤怠だけ取得
        $attendances = Attendance::with('user')
            ->whereDate('work_date', $date)
            ->get();

        return view('admin.attendance.list', compact('attendances', 'date'));
    }

    // 管理者：勤怠詳細画面を表示する
    public function adminDetail($id)
    {
        // 指定されたIDの勤怠データを取得する
        // with('user', 'breaks') で「ユーザー情報」と「休憩データ」も一緒に取得
        // → 画面で名前や休憩時間を表示するため
        $attendance = Attendance::with('user', 'breaks')->findOrFail($id);

        // 管理者用の詳細画面にデータを渡して表示する
        // compact('attendance') で $attendance をそのままビューに渡す
        return view('admin.attendance.detail', compact('attendance'));
    }

    public function staffAttendance(Request $request, $id)
    {
        // 対象ユーザー取得
        $user = User::findOrFail($id);

        // クエリから年月取得（例: 2026-05）
        $monthParam = $request->input('month');

        // 月が指定されていればそれ、なければ今月
        $date = $monthParam
            ? Carbon::createFromFormat('Y-m', $monthParam)
            : Carbon::now();

        // 年と月を取り出す
        $year = $date->year;
        $month = $date->month;

        // 指定された月の勤怠を取得
        $attendances = Attendance::where('user_id', $id)
            ->whereYear('work_date', $year)
            ->whereMonth('work_date', $month)
            ->get();

        return view('admin.attendance.staff', compact('user', 'attendances', 'year', 'month'));
    }

    // スタッフ別勤怠一覧のCSV出力
    public function exportCsv($id, $year, $month)
    {
        // 指定されたスタッフ情報を取得
        $user = User::findOrFail($id);

        // 指定された年・月の勤怠データを取得
        $attendances = Attendance::where('user_id', $id)
            ->whereYear('work_date', $year)
            ->whereMonth('work_date', $month)
            ->get();

        // ダウンロードするCSVファイル名を作成
        $fileName = $user->name . '_' . $year . '_' . $month . '_attendance.csv';

        // ダウンロード時の設定
        $headers = [
            // CSVファイルですよという指定
            'Content-Type' => 'text/csv',

            // 添付ファイルとしてダウンロードさせる指定
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        // CSVの中身を作る処理
        $callback = function () use ($attendances) {

            // 出力先を開く
            $file = fopen('php://output', 'w');

            // 1行目（見出し）
            fputcsv($file, ['日付', '出勤', '退勤']);

            // 勤怠データを1件ずつCSVに書き込む
            foreach ($attendances as $attendance) {

                fputcsv($file, [

                    // 日付
                    $attendance->work_date,

                    // 出勤時間（あれば表示）
                    optional($attendance->start_time)->format('H:i'),

                    // 退勤時間（あれば表示）
                    optional($attendance->end_time)->format('H:i'),
                ]);
            }

            // ファイルを閉じる
            fclose($file);
        };

        // CSVファイルをダウンロードさせる
        return response()->stream($callback, 200, $headers);
    }
}
