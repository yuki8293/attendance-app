<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use App\Http\Requests\StampCorrectionRequest;
use App\Models\Attendance;

class StampCorrectionRequestController extends Controller
{
    // 一般ユーザー用の申請表示
    public function index()
    {
        // 承認待ちの申請データを取得
        $pendingRequests = AttendanceRequest::with(['user', 'attendance'])
            // userテーブル（名前）とattendanceテーブル（日付）も一緒に取得（リレーション）
            ->where('user_id', auth()->id())
            // ログインしているユーザーの申請だけ取得
            ->where('status', '承認待ち')
            // ステータスが「承認待ち」のものだけ取得
            ->get();
        // データを全部取得（コレクションとして取得）

        // 承認済みの申請データを取得
        $approvedRequests = AttendanceRequest::with(['user', 'attendance'])
            // userとattendanceの情報も一緒に取得
            ->where('user_id', auth()->id())
            // ログインユーザーのデータだけ
            ->where('status', '承認済み')
            // ステータスが「承認済み」のもの
            ->get();
        // データ取得

        return view('stamp_correction_request.list', compact('pendingRequests', 'approvedRequests'));
    }

    // 申請を保存する処理
    public function store(StampCorrectionRequest $request)
    {

        // AttendanceRequestテーブルにデータを新規登録
        AttendanceRequest::create([

            // ログインしているユーザーのIDを保存
            'user_id' => auth()->id(),
            // 対象となる勤怠データのID
            'attendance_id' => $request->attendance_id,
            // 修正後の出勤時間
            'start_time' => $request->start_time,
            // 修正後の退勤時間
            'end_time' => $request->end_time,
            // 修正理由
            'note' => $request->note,

            'status' => '承認待ち',

            'break_start' => $request->input('breaks.0.start'),
            'break_end'   => $request->input('breaks.0.end'),
        ]);

        // 対象の勤怠データを取得（修正申請の対象になっている勤怠
        $attendance = Attendance::find($request->attendance_id);

        // 勤怠テーブルの備考を更新
        // ※詳細画面では attendance テーブルの note を表示しているため、
        // ここも更新しないと画面に反映されない
        $attendance->update([
            'note' => $request->note,
        ]);

        // 保存後、申請一覧画面にリダイレクト
        return redirect()->route('stamp_correction_request.list');
    }

    // 管理者：申請一覧ページ表示
    public function list()
    {
        // 承認待ちの申請
        $pendingRequests = \App\Models\AttendanceRequest::with(['user', 'attendance'])
            ->where('status', '承認待ち')
            ->get();

        // 承認済みの申請
        $approvedRequests = \App\Models\AttendanceRequest::with(['user', 'attendance'])
            ->where('status', '承認済み')
            ->get();

        // 画面に渡す
        return view('admin.stamp_request.list', compact('pendingRequests', 'approvedRequests'));
    }

    // 管理者：修正申請の詳細表示（承認画面）
    public function approve($attendance_correct_request_id)
    {
        // 修正申請データを取得（userとattendanceも一緒に取得）
        // with(['user', 'attendance'])でリレーションを事前読み込み（N+1問題対策）
        $attendance_correct_request = AttendanceRequest::with(['user', 'attendance'])
            // 指定されたIDのデータを取得（なければ404エラー）
            ->findOrFail($attendance_correct_request_id);

        // 取得したデータを承認画面（Blade）に渡して表示
        // compactで変数名そのまま渡せる
        return view('admin.stamp_request.approve', compact('attendance_correct_request'));
    }

    // 管理者：承認処理
    public function updateStatus($id)
    {
        // 修正申請データ取得
        $requestData = AttendanceRequest::with('attendance.breaks')
            ->findOrFail($id);

        // 元の勤怠データ取得
        $attendance = $requestData->attendance;

        // 勤怠更新
        $attendance->update([
            'start_time' => $requestData->start_time,
            'end_time'   => $requestData->end_time,
            'note'       => $requestData->note,
        ]);

        // 休憩更新
        $break = $attendance->breaks()->first();

        if ($break && $requestData->break_start && $requestData->break_end) {

            $break->update([
                'start_time' => $requestData->break_start,
                'end_time'   => $requestData->break_end,
            ]);
        }

        // 承認済みに変更
        $requestData->status = '承認済み';
        $requestData->save();

        return response()->json([
            'status' => 'ok'
        ]);
    }
}
