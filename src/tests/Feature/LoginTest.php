<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_email_is_required_for_login()
    {
        // メール未入力でログインした場合、エラーになるか確認
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        // emailにエラーがあるか確認
        $response->assertSessionHasErrors('email');
    }

    public function test_password_is_required_for_login()
    {
        // パスワード未入力でログインした場合、エラーになるか確認
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        // passwordにエラーがあるか確認
        $response->assertSessionHasErrors('password');
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        // 存在しないユーザー情報でログインした場合、失敗するか確認
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'password',
        ]);

        // セッションにエラーがあるか確認
        $response->assertSessionHasErrors();
    }
}
