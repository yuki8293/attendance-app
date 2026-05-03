<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 管理者が勤怠詳細画面を正しく閲覧できるか
     */
    public function test_admin_can_view_attendance_detail()
    {
        // 管理者・一般ユーザー・勤怠データ作成
        $admin = Admin::factory()->create();

        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-04-01',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 管理者として詳細画面へアクセス
        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.detail', $attendance->id));

        // 正常に表示され、内容が一致しているか確認
        $response->assertStatus(200);
        $response->assertSee('2026-04-01');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /**
     * 出勤時間が退勤時間より後の場合エラーになるか
     */
    public function test_start_time_after_end_time_returns_error()
    {
        $admin = Admin::factory()->create();

        $attendance = Attendance::factory()->create();

        // 不正データ（出勤 > 退勤）
        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.attendance.update', $attendance->id), [
                'start_time' => '19:00',
                'end_time' => '18:00',
            ]);

        // バリデーションエラー確認
        $response->assertSessionHasErrors([
            'start_time' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    /**
     * 休憩開始時間が退勤時間より後の場合エラーになるか
     */
    public function test_break_start_after_end_time_error()
    {
        $admin = Admin::factory()->create();

        $attendance = Attendance::factory()->create();

        // 不正データ（休憩開始 > 退勤）
        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.attendance.update', $attendance->id), [
                'end_time' => '18:00',
                'break_start' => '19:00',
            ]);

        // バリデーションエラー確認
        $response->assertSessionHasErrors([
            'break_start' => '休憩時間が不適切な値です',
        ]);
    }

    /**
     * 休憩終了時間が退勤時間より後の場合エラーになるか
     */
    public function test_break_end_after_end_time_error()
    {
        $admin = Admin::factory()->create();

        $attendance = Attendance::factory()->create();

        // 不正データ（休憩終了 > 退勤）
        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.attendance.update', $attendance->id), [
                'end_time' => '18:00',
                'break_end' => '19:00',
            ]);

        // バリデーションエラー確認
        $response->assertSessionHasErrors([
            'break_end' => '休憩時間もしくは退勤時間が不適切な値です',
        ]);
    }

    /**
     * 備考が未入力の場合エラーになるか
     */
    public function test_note_is_required()
    {
        $admin = Admin::factory()->create();

        $attendance = Attendance::factory()->create();

        // 備考未入力
        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.attendance.update', $attendance->id), [
                'note' => '',
            ]);

        // バリデーションエラー確認
        $response->assertSessionHasErrors([
            'note' => '備考を記入してください',
        ]);
    }
}
