<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    // 自分の勤怠情報が一覧画面に表示されるか確認する
    public function test_user_attendances_are_displayed()
    {
        // テスト用ユーザーを作成する
        $user = User::factory()->create();

        // テスト用の勤怠データを作成する
        // start_time と end_time を入れることで「出勤・退勤済」の状態を作る
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now(),
            'end_time' => now(),
        ]);

        // ログインした状態で勤怠一覧画面にアクセスする
        $response = $this->actingAs($user)->get('/attendance/list');

        // ページが正常に表示されているか確認する（HTTPステータス200）
        $response->assertStatus(200);

        // 作成した勤怠データの時刻が画面に表示されているか確認する
        // 今回は時刻（H:i形式）が表示されていればOKとする
        $response->assertSee(now()->format('H:i'));
    }

    // 勤怠一覧画面に現在の月が表示されるか確認する
    public function test_current_month_is_displayed()
    {
        // 現在日時を固定する（テストを安定させるため）
        // 例：2026年4月として扱う
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::create(2026, 4, 1));

        // テスト用ユーザーを作成する
        $user = User::factory()->create();

        // ログインした状態で勤怠一覧画面にアクセスする
        $response = $this->actingAs($user)->get('/attendance/list');

        // ページが正常に表示されているか確認する
        $response->assertStatus(200);

        // 画面に「2026年4月」が表示されているか確認する
        $response->assertSee('2026年04月');
    }

    // 「前月」を押したときに前月の情報が表示されるか確認する
    public function test_previous_month_is_displayed()
    {
        // 現在日時を固定する（2026年4月として扱う）
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::create(2026, 4, 15));

        // テスト用ユーザーを作成する
        $user = User::factory()->create();

        // 前月（3月）の勤怠データを作成する
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-03-10',
            'start_time' => now(),
            'end_time' => now(),
        ]);

        // 「前月」リンクを押した想定でクエリを付けてアクセスする
        $response = $this->actingAs($user)->get('/attendance/list?month=2026-03');

        // ページが正常に表示されるか確認する
        $response->assertStatus(200);

        // 画面に「2026年03月」が表示されているか確認する
        $response->assertSee('2026年03月');
    }

    // 「翌月」を押したときに翌月の情報が表示されるか確認する
    public function test_next_month_is_displayed()
    {
        // 現在日時を固定（2026年4月）
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::create(2026, 4, 15));

        // テスト用ユーザー
        $user = User::factory()->create();

        // 翌月（5月）の勤怠データ
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-05-10',
            'start_time' => now(),
            'end_time' => now(),
        ]);

        // 翌月のURLにアクセス
        $response = $this->actingAs($user)->get('/attendance/list?month=2026-05');

        $response->assertStatus(200);

        // 「2026年05月」が表示されているか
        $response->assertSee('2026年05月');
    }

    // 「詳細」を押すと勤怠詳細画面に遷移できるか確認する
    public function test_can_navigate_to_attendance_detail()
    {
        // テスト用ユーザーを作成する
        $user = User::factory()->create();

        // 勤怠データを作成する
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now(),
        ]);

        // 勤怠一覧画面を開く
        $response = $this->actingAs($user)->get('/attendance/list');

        // 一覧画面に「詳細リンク」が表示されているか確認する
        // → ボタンを押せる状態かどうかの確認
        $response->assertSee('/attendance/detail/' . $attendance->id);

        // 実際に詳細ページへアクセスする
        $detailResponse = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        // 正常にページが表示されるか確認する
        $detailResponse->assertStatus(200);
    }
}
