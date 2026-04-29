<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceBreakTest extends TestCase
{
    use RefreshDatabase;

    // 出勤中ユーザーに休憩入ボタンが表示されるか確認する
    public function test_break_start_button_is_displayed()
    {
        // テスト用ユーザーを作成する
        $user = User::factory()->create();

        // 出勤中の勤怠データを作成する
        // start_time が入っているため出勤中の状態になる
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now(),
        ]);

        // 勤怠打刻画面を開く
        $response = $this->actingAs($user)->get('/attendance');

        // 画面に休憩入ボタンが表示されているか確認する
        $response->assertSee('休憩入');
    }

    // 休憩入の処理後にステータスが休憩中になるか確認する
    public function test_user_can_start_break()
    {
        // テスト用ユーザーを作成する
        $user = User::factory()->create();

        // 出勤中の勤怠データを作成する
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now(),
        ]);

        // 休憩入の処理を行う
        $this->actingAs($user)->post('/attendance', [
            'action' => 'break_start',
        ]);

        // 勤怠打刻画面を開く
        $response = $this->actingAs($user)->get('/attendance');

        // ステータスが休憩中になっているか確認する
        $response->assertSee('休憩中');
    }

    // 休憩入と休憩戻のあと再び休憩入ボタンが表示されるか確認する
    public function test_user_can_take_break_many_times()
    {
        // テスト用ユーザーを作成する
        $user = User::factory()->create();

        // 出勤中の勤怠データを作成する
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now(),
        ]);

        // 1回目の休憩入を行う
        $this->actingAs($user)->post('/attendance', [
            'action' => 'break_start',
        ]);

        // 1回目の休憩戻を行う
        $this->actingAs($user)->post('/attendance', [
            'action' => 'break_end',
        ]);

        // 勤怠打刻画面を開く
        $response = $this->actingAs($user)->get('/attendance');

        // 再び休憩入ボタンが表示されているか確認する
        $response->assertSee('休憩入');
    }

    // 休憩戻の処理後にステータスが出勤中になるか確認する
    public function test_user_can_end_break()
    {
        // テスト用ユーザーを作成する
        $user = User::factory()->create();

        // 出勤中の勤怠データを作成する
        // start_time が入っているので出勤中の状態になる
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now(),
        ]);

        // まず休憩入の処理を行う
        $this->actingAs($user)->post('/attendance', [
            'action' => 'break_start',
        ]);

        // 次に休憩戻の処理を行う
        $this->actingAs($user)->post('/attendance', [
            'action' => 'break_end',
        ]);

        // 勤怠打刻画面を開く
        $response = $this->actingAs($user)->get('/attendance');

        // ステータスが出勤中に戻っているか確認する
        $response->assertSee('出勤中');
    }

    // 休憩時刻が勤怠一覧画面で確認できるか確認する
    public function test_break_time_is_shown_in_attendance_list()
    {
        // テスト用ユーザーを作成する
        $user = User::factory()->create();

        // 出勤中の勤怠データを作成する
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now(),
        ]);

        // 休憩入の処理を行う
        $this->actingAs($user)->post('/attendance', [
            'action' => 'break_start',
        ]);

        // 休憩戻の処理を行う
        $this->actingAs($user)->post('/attendance', [
            'action' => 'break_end',
        ]);

        // 勤怠一覧画面を開く
        $response = $this->actingAs($user)->get('/attendance/list');

        // 一覧画面に休憩時刻が表示されているか確認する
        $response->assertSee(now()->format('H:i'));
    }
}
