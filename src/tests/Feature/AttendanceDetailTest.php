<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    // 勤怠詳細画面にログインユーザーの名前が表示されるか確認する
    public function test_user_name_is_displayed()
    {
        // テスト用ユーザーを作成（名前を固定）
        $user = User::factory()->create([
            'name' => '山田太郎',
        ]);

        // 勤怠データを作成する
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now(),
        ]);

        // ログインして勤怠詳細画面にアクセスする
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        // ページが正常に表示されるか確認する
        $response->assertStatus(200);

        // 画面にユーザー名が表示されているか確認する
        $response->assertSee('山田太郎');
    }

    // 勤怠詳細画面に正しい日付が表示されるか確認する
    public function test_work_date_is_displayed()
    {
        // 日付を固定する（テストを安定させる）
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::create(2026, 4, 15));

        // ユーザー作成
        $user = User::factory()->create();

        // 勤怠データ作成（特定の日付を指定）
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-04-10',
            'start_time' => now(),
        ]);

        // 詳細画面へアクセス
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        // 正常表示確認
        $response->assertStatus(200);

        // 画面に「2026/04/10」などが表示されているか確認
        // ※表示形式に合わせてここは調整
        $response->assertSee('2026/04/10');
    }

    // 勤怠詳細画面に出勤・退勤時間が正しく表示されるか確認する
    public function test_start_and_end_time_are_displayed()
    {
        // 時刻を固定（テスト安定のため）
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::create(2026, 4, 15, 9, 0, 0));

        // ユーザー作成
        $user = User::factory()->create();

        // 勤怠データ作成（出勤・退勤あり）
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-04-10',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 詳細画面へアクセス
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        // 正常表示確認
        $response->assertStatus(200);

        // 出勤時間が表示されているか確認
        $response->assertSee('09:00');

        // 退勤時間が表示されているか確認
        $response->assertSee('18:00');
    }

    // 勤怠詳細画面に休憩時間が正しく表示されるか確認する
    public function test_break_time_is_displayed()
    {
        // ユーザー作成
        $user = User::factory()->create();

        // 勤怠データ作成
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now(),
        ]);

        // 休憩データを作成
        \App\Models\BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
        ]);

        // 詳細画面へアクセス
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        // 正常表示確認
        $response->assertStatus(200);

        // 休憩開始時間が表示されているか
        $response->assertSee('12:00');

        // 休憩終了時間が表示されているか
        $response->assertSee('13:00');
    }
}
