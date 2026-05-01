<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    // 管理者がログインした状態で、今日の全ユーザーの勤怠一覧が正しく表示されることを確認するテスト
    public function test_admin_can_see_today_all_users_attendance()
    {
        // 管理者ユーザー作成
        $admin = User::factory()->create([
            'is_admin' => true, // 管理者フラグ（※あなたの実装に合わせて調整）
        ]);

        // 一般ユーザー作成
        $user = User::factory()->create();

        // 今日の勤怠データ作成（表示される対象）
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => Carbon::today(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 管理者としてログインして勤怠一覧へアクセス
        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/list');

        // 正常に画面が表示されることを確認
        $response->assertStatus(200);

        // 今日の日付が画面に表示されていることを確認
        $response->assertSee(Carbon::today()->format('Y年m月d日'));

        // 作成した勤怠データが表示されていることを確認
        $response->assertSee($user->name);
    }

    // 管理者が前日ボタン（または日付指定）を使ったときに、前日の勤怠一覧が正しく表示されることを確認するテスト
    public function test_admin_can_navigate_to_previous_day()
    {
        // 管理者作成
        $admin = User::factory()->create(['is_admin' => true]);

        // 昨日の勤怠データ作成
        $yesterday = Carbon::yesterday();

        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $yesterday,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 前日指定でアクセス（?date=）
        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/list?date=' . $yesterday->format('Y-m-d'));

        // 正常表示確認
        $response->assertStatus(200);

        // 日付が前日になっていることを確認
        $response->assertSee($yesterday->format('Y年m月d日'));
    }

    // 管理者が翌日ボタン（または日付指定）を使ったときに、翌日の勤怠一覧が正しく表示されることを確認するテスト
    public function test_admin_can_navigate_to_next_day()
    {
        // 管理者作成
        $admin = User::factory()->create(['is_admin' => true]);

        // 明日データ
        $tomorrow = Carbon::tomorrow();

        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $tomorrow,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 翌日アクセス
        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/list?date=' . $tomorrow->format('Y-m-d'));

        // 正常表示確認
        $response->assertStatus(200);

        // 日付が翌日になっていることを確認
        $response->assertSee($tomorrow->format('Y年m月d日'));
    }
}
