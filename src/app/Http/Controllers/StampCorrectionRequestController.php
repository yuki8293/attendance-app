<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use App\Http\Requests\StampCorrectionRequest;

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
            'user_id' => auth()->id(),
            // ログインしているユーザーのIDを保存

            'attendance_id' => $request->attendance_id,
            // 対象となる勤怠データのID

            'start_time' => $request->start_time,
            // 修正後の出勤時間

            'end_time' => $request->end_time,
            // 修正後の退勤時間

            'note' => $request->note,
            // 修正理由

            'status' => '承認待ち',
            // ステータス（これがないと一覧に表示されない⚠️）
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
        $request = \App\Models\AttendanceRequest::findOrFail($id);

        $request->status = '承認済み';
        $request->save();

        return response()->json([
            'status' => 'ok'
        ]);
    }
}
