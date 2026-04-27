<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;

class AttendanceDateTimeTest extends TestCase
{
    use RefreshDatabase;

    // 勤怠打刻画面を開いたときに画面上に現在日時が正しく表示されるかのテスト
    public function test_current_datetime_is_displayed()
    {
        // 現在日時を固定する
        // テスト中は 2026/04/27 09:15:00 として扱われる
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 9, 15, 0));

        // テスト用ユーザーを作成する
        $user = User::factory()->create();

        // ログインした状態で勤怠打刻画面へアクセスする
        $response = $this->actingAs($user)->get('/attendance');

        // 画面が正常に表示されたか確認する（HTTPステータス200）
        $response->assertStatus(200);

        // 画面に現在日付が表示されているか確認する
        $response->assertSee('2026年4月27日');

        // 画面に現在時刻が表示されているか確認する
        $response->assertSee('09:15');

        // 曜日も表示している場合は確認する
        // 月曜日なので「月」が表示される想定
        $response->assertSee('月');
    }
}
