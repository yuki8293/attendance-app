<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceCorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    // 勤怠詳細の修正申請が正常に作成されることを確認するテスト
    public function test_correction_request_is_created()
    {
        // テスト用ユーザーを作成 
        $user = User::factory()->create();

        // 修正対象の勤怠データを作成 
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 勤怠詳細の修正申請を実行 
        $this->actingAs($user)
            ->put('/attendance/' . $attendance->id, [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'note' => '修正申請テスト',
            ]);

        // 修正申請がDBに保存されているか確認 
        $this->assertDatabaseHas('attendance_requests', [
            'attendance_id' => $attendance->id,
            'status' => '承認待ち',
        ]);
    }

    // 出勤時間が退勤時間より後の場合にエラーになることを確認
    public function test_start_time_after_end_time_fails()
    {
        // ユーザー作成
        $user = User::factory()->create();

        // 勤怠作成
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 異常データ（出勤 > 退勤）
        $response = $this->actingAs($user)
            ->from('/attendance/detail/' . $attendance->id)
            ->put('/attendance/' . $attendance->id, [
                'clock_in' => '19:00',
                'clock_out' => '18:00',
                'note' => 'テスト',
            ]);

        // 元の画面に戻る（バリデーションエラー）
        $response->assertRedirect('/attendance/detail/' . $attendance->id);

        // エラーがあることを確認
        $response->assertSessionHasErrors(['clock_out']);
    }

    // 休憩開始時間が退勤時間より後の場合にエラーになることを確認
    public function test_break_start_after_end_time_fails()
    {
        // テスト用ユーザーを作成（ログイン用）
        $user = User::factory()->create();

        // 修正対象となる勤怠データを作成
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 不正なデータで更新処理を実行（休憩開始が退勤より後）
        $response = $this->actingAs($user)
            ->from('/attendance/detail/' . $attendance->id) // エラー時に戻る画面
            ->put('/attendance/' . $attendance->id, [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'note' => 'テスト',

                // 退勤時間より後の休憩開始 → 本来はエラーになるべき
                'breaks' => [
                    [
                        'start' => '19:00',
                        'end' => '19:30',
                    ]
                ]
            ]);

        // バリデーションエラーなので元の画面に戻る
        $response->assertRedirect('/attendance/detail/' . $attendance->id);

        // エラーが発生していることを確認
        $response->assertSessionHasErrors();
    }

    // 休憩終了時間が退勤時間より後の場合にエラーになることを確認
    public function test_break_end_after_end_time_fails()
    {
        // ユーザー作成
        $user = User::factory()->create();

        // 勤怠作成
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 異常データ（休憩終了が退勤より後）
        $response = $this->actingAs($user)
            ->from('/attendance/detail/' . $attendance->id)
            ->put('/attendance/' . $attendance->id, [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'note' => 'テスト',
                'breaks' => [
                    [
                        'start' => '12:00',
                        'end' => '19:00', // ← ここがアウト
                    ]
                ]
            ]);

        // 元の画面に戻る
        $response->assertRedirect('/attendance/detail/' . $attendance->id);

        // エラーがあることを確認
        $response->assertSessionHasErrors();
    }

    // 備考が未入力の場合にエラーになることを確認
    public function test_note_is_required()
    {
        // ユーザー作成
        $user = User::factory()->create();

        // 勤怠作成
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // noteなしで送信
        $response = $this->actingAs($user)
            ->from('/attendance/detail/' . $attendance->id)
            ->put('/attendance/' . $attendance->id, [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'note' => '', // ← 未入力
            ]);

        // 元の画面に戻る
        $response->assertRedirect('/attendance/detail/' . $attendance->id);

        // noteエラー確認
        $response->assertSessionHasErrors(['note']);
    }
    // 承認待ち一覧に自分の申請が表示されることを確認
    public function test_pending_requests_are_displayed()
    {
        // ユーザー作成
        $user = User::factory()->create();

        // 勤怠作成
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 修正申請作成
        \App\Models\AttendanceRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'start_time' => '09:00',
            'end_time' => '18:00',
            'note' => 'テスト申請',
            'status' => '承認待ち',
        ]);

        // 申請一覧ページにアクセス
        $response = $this->actingAs($user)
            ->get('/stamp_correction_request/list');

        // 正常表示
        $response->assertStatus(200);

        // 作成した申請が表示されているか確認
        $response->assertSee('テスト申請');
    }

    // 承認済み一覧に申請が表示されることを確認
    public function test_approved_requests_are_displayed()
    {
        // ユーザー作成
        $user = User::factory()->create();

        // 勤怠作成
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 承認済み申請作成
        \App\Models\AttendanceRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'start_time' => '09:00',
            'end_time' => '18:00',
            'note' => '承認済みテスト',
            'status' => '承認済み',
        ]);

        // 申請一覧ページにアクセス
        $response = $this->actingAs($user)
            ->get('/stamp_correction_request/list');

        // 正常表示
        $response->assertStatus(200);

        // 表示確認
        $response->assertSee('承認済みテスト');
    }

    // 申請一覧の詳細から勤怠詳細画面に遷移できることを確認
    public function test_can_navigate_to_attendance_detail_from_request()
    {
        // ユーザー作成
        $user = User::factory()->create();

        // 勤怠作成
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 修正申請作成
        \App\Models\AttendanceRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'start_time' => '09:00',
            'end_time' => '18:00',
            'note' => '詳細テスト',
            'status' => '承認待ち',
        ]);

        // 勤怠詳細ページにアクセス（詳細ボタンの遷移先）
        $response = $this->actingAs($user)
            ->get('/attendance/detail/' . $attendance->id);

        // 正常表示されることを確認
        $response->assertStatus(200);

        // データが表示されていることを確認
        $response->assertSee($attendance->user->name);
    }
}
