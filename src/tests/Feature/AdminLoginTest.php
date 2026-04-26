<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    public function test_email_is_required_for_admin_login()
    {
        // メール未入力で管理者ログインした場合
        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => 'password',
        ]);

        // emailエラー確認
        $response->assertSessionHasErrors('email');
    }

    public function test_password_is_required_for_admin_login()
    {
        // パスワード未入力で管理者ログインした場合
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => '',
        ]);

        // passwordエラー確認
        $response->assertSessionHasErrors('password');
    }

    public function test_admin_cannot_login_with_invalid_credentials()
    {
        // 存在しない管理者情報でログインした場合
        $response = $this->post('/admin/login', [
            'email' => 'wrong@example.com',
            'password' => 'password',
        ]);

        // エラー確認
        $response->assertSessionHasErrors();
    }
}
