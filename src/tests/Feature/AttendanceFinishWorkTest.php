<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceFinishWorkTest extends TestCase
{
    use RefreshDatabase;

    // 出勤中ユーザーに退勤ボタンが表示されるか確認する
    public function test_finish_button_is_displayed()
    {
        // テスト用ユーザーを作成する
        $user = User::factory()->create();

        // 出勤中の勤怠データを作成する（start_timeあり = 出勤中）
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now(),
        ]);

        // 勤怠打刻画面を開く
        $response = $this->actingAs($user)->get('/attendance');

        // 退勤ボタンが表示されているか確認する
        $response->assertSee('退勤');
    }

    // 退勤処理後にステータスが退勤済になるか確認する
    public function test_user_can_finish_work()
    {
        // テスト用ユーザーを作成する
        $user = User::factory()->create();

        // 出勤中の状態を作る
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now(),
        ]);

        // 退勤処理を実行する
        $this->actingAs($user)->post('/attendance', [
            'action' => 'end',
        ]);

        // 勤怠打刻画面を再表示する
        $response = $this->actingAs($user)->get('/attendance');

        // ステータスが退勤済になっているか確認する
        $response->assertSee('退勤済');
    }

    // 退勤時刻が勤怠一覧画面で確認できるか確認する
    public function test_finish_time_is_shown_in_attendance_list()
    {
        // テスト用ユーザーを作成する
        $user = User::factory()->create();

        // 出勤処理を行う
        $this->actingAs($user)->post('/attendance', [
            'action' => 'start',
        ]);

        // 退勤処理を行う
        $this->actingAs($user)->post('/attendance', [
            'action' => 'end',
        ]);

        // 勤怠一覧画面を開く
        $response = $this->actingAs($user)->get('/attendance/list');

        // 一覧画面に退勤時刻が表示されているか確認する
        $response->assertSee(now()->format('H:i'));
    }
}
