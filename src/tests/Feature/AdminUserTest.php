<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminUserTest extends TestCase
{

    use RefreshDatabase;

    /**
     * 管理者が全ユーザーの氏名・メールを確認できる
     */
    public function test_admin_can_view_all_users()
    {
        // 管理者ユーザーを作成
        $admin = Admin::factory()->create();

        // 一般ユーザーを複数作成
        $users = User::factory()->count(3)->create();

        // 管理者としてログイン（adminガード重要！）
        $this->actingAs($admin, 'admin');

        // スタッフ一覧ページへアクセス
        $response = $this->get('/admin/staff/list');

        // 正常レスポンス確認
        $response->assertStatus(200);

        // 全ユーザーの氏名・メールが表示されているか確認
        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    /**
     * 管理者がユーザーの勤怠一覧を確認できる
     */
    public function test_admin_can_view_user_attendance()
    {
        // 管理者作成
        $admin = Admin::factory()->create();

        // ユーザー作成
        $user = User::factory()->create();

        // 勤怠データ作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::today(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 管理者ログイン
        $this->actingAs($admin, 'admin');

        // スタッフ別勤怠一覧ページへアクセス
        $response = $this->get("/admin/attendance/staff/{$user->id}");

        // 正常確認
        $response->assertStatus(200);

        // 勤怠情報が表示されているか確認
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /**
     * 前月ボタンで前月の勤怠が表示される
     */
    public function test_admin_can_view_previous_month()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();

        // 前月のデータ作成
        $lastMonth = Carbon::now()->subMonth();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $lastMonth,
        ]);

        $this->actingAs($admin, 'admin');

        // 前月パラメータ付きでアクセス（ここは実装に合わせて調整）
        $response = $this->get("/admin/attendance/staff/{$user->id}?month=" . $lastMonth->format('Y-m'));

        $response->assertStatus(200);

        // 前月の日付が表示されているか
        $response->assertSee($lastMonth->format('m'));
    }

    /**
     * 翌月ボタンで翌月の勤怠が表示される
     */
    public function test_admin_can_view_next_month()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();

        // 翌月のデータ
        $nextMonth = Carbon::now()->addMonth();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $nextMonth,
        ]);

        $this->actingAs($admin, 'admin');

        $response = $this->get("/admin/attendance/staff/{$user->id}?month=" . $nextMonth->format('Y-m'));

        $response->assertStatus(200);

        $response->assertSee($nextMonth->format('m'));
    }

    /**
     * 詳細ボタンで勤怠詳細画面へ遷移できる
     */
    public function test_admin_can_access_attendance_detail()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();

        // 勤怠データ作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($admin, 'admin');

        // 詳細ページへアクセス
        $response = $this->get("/admin/attendance/{$attendance->id}");

        // 正常に表示されるか
        $response->assertStatus(200);
    }

    public function test_admin_can_see_detail_link()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($admin, 'admin');

        $response = $this->get("/admin/attendance/staff/{$user->id}");

        // 詳細リンクが表示されているか
        $response->assertSee("/admin/attendance/{$attendance->id}");
    }
}
