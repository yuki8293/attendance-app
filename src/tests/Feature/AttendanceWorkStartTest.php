<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceWorkStartTest extends TestCase
{
    use RefreshDatabase;

    // 勤務外ユーザーには出勤ボタンが表示される
    public function test_start_button_is_displayed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('出勤');
    }

    // 出勤処理後にステータスが出勤中になる
    public function test_user_can_start_work()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/attendance', [
            'action' => 'start',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('出勤中');
    }

    // 退勤済ユーザーには出勤ボタンが表示されない
    public function test_user_cannot_start_twice()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now(),
            'end_time' => now(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertDontSee('出勤');
    }

    // 出勤時刻が勤怠一覧画面で確認できる
    public function test_start_time_is_shown_in_attendance_list()
    {
        // テスト用ユーザー作成
        $user = User::factory()->create();

        // ログインして出勤処理を行う
        $this->actingAs($user)->post('/attendance', [
            'action' => 'start',
        ]);

        // 勤怠一覧画面へアクセス
        $response = $this->actingAs($user)->get('/attendance/list');

        // 一覧画面に出勤時刻が表示されているか確認
        $response->assertSee(now()->format('H:i'));
    }
}
