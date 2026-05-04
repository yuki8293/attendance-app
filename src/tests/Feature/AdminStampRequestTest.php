<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceRequest;

class AdminStampRequestTest extends TestCase
{

    use RefreshDatabase;

    /**
     * 【管理者】承認待ちの修正申請一覧が表示される
     *
     * ■テスト内容
     * ・管理者でログイン
     * ・承認待ちの修正申請を複数作成
     * ・一覧ページを開く
     * ・作成した申請がすべて表示されることを確認
     */
    public function test_admin_can_view_pending_requests()
    {
        // ▼ 管理者ユーザーを作成
        $admin = Admin::factory()->create();

        // ▼ 一般ユーザーと勤怠データを作成
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id
        ]);

        // ▼ 承認待ちの修正申請を複数作成
        $requests = AttendanceRequest::factory()->count(2)->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => '承認待ち',
        ]);

        // ▼ 管理者としてログイン（adminガード指定）
        $this->actingAs($admin, 'admin');

        // ▼ 修正申請一覧ページへアクセス
        $response = $this->get('/admin/stamp_correction_request/list');

        // ▼ 正常にページが表示されることを確認
        $response->assertStatus(200);

        // ▼ 作成した申請が画面に表示されているか確認
        foreach ($requests as $request) {
            $response->assertSee($request->note);
        }
    }

    /**
     * 【管理者】承認済みの修正申請一覧が表示される
     *
     * ■テスト内容
     * ・承認済みのデータを作成
     * ・一覧画面に表示されることを確認
     */
    public function test_admin_can_view_approved_requests()
    {
        // ▼ 管理者作成
        $admin = Admin::factory()->create();

        // ▼ 一般ユーザーと勤怠
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id
        ]);

        // ▼ 承認済みデータを作成
        $requests = AttendanceRequest::factory()->count(2)->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => '承認済み',
        ]);

        // ▼ 管理者ログイン
        $this->actingAs($admin, 'admin');

        // ▼ 一覧ページへアクセス
        $response = $this->get('/admin/stamp_correction_request/list');

        // ▼ 正常表示確認
        $response->assertStatus(200);

        // ▼ 承認済みデータが表示されているか確認
        foreach ($requests as $request) {
            $response->assertSee($request->note);
        }
    }

    /**
     * 【管理者】修正申請の詳細画面が正しく表示される
     *
     * ■テスト内容
     * ・申請データを作成
     * ・詳細画面にアクセス
     * ・申請内容（備考など）が表示されることを確認
     */
    public function test_admin_can_view_request_detail()
    {
        // ▼ 管理者作成
        $admin = Admin::factory()->create();

        // ▼ ユーザーと勤怠作成
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id
        ]);

        // ▼ 修正申請データ作成
        $requestData = AttendanceRequest::factory()->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'note' => 'テスト申請',
        ]);

        // ▼ 管理者ログイン
        $this->actingAs($admin, 'admin');

        // ▼ 詳細画面へアクセス
        $response = $this->get("/admin/stamp_correction_request/approve/{$requestData->id}");

        // ▼ 正常表示確認
        $response->assertStatus(200);

        // ▼ 申請内容が画面に表示されているか確認
        $response->assertSee('テスト申請');
    }

    /**
     * 【管理者】修正申請を承認できる
     *
     * ■テスト内容
     * ・承認待ちの申請を作成
     * ・承認処理を実行
     * ・ステータスが「承認済み」に更新されることを確認
     */
    public function test_admin_can_approve_request()
    {
        // ▼ 管理者作成
        $admin = Admin::factory()->create();

        // ▼ ユーザーと勤怠作成
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        // ▼ 承認待ちの修正申請を作成
        $requestData = AttendanceRequest::factory()->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => '承認待ち',
        ]);

        // ▼ 管理者ログイン
        $this->actingAs($admin, 'admin');

        // ▼ 承認処理を実行（PUTリクエスト）
        $response = $this->post("/admin/stamp_correction_request/approve/{$requestData->id}");

        // ▼ 正常レスポンス確認（Ajax想定）
        $response->assertStatus(200);

        // ▼ ステータスがDB上で更新されているか確認
        $this->assertDatabaseHas('attendance_requests', [
            'id' => $requestData->id,
            'status' => '承認済み',
        ]);
    }
}
