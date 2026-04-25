<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegisterTest extends TestCase
{

    public function test_name_is_required()
    {
        // 名前未入力で会員登録した場合、バリデーションエラーになるか確認
        $response = $this->post('/register', [
            'name' => '', // 名前を空にする
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // nameフィールドにエラーが入っているか確認
        $response->assertSessionHasErrors('name');
    }

    public function test_email_is_required()
    {
        // メール未入力で会員登録した場合、バリデーションエラーになるか確認
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // emailフィールドにエラーが入っているか確認
        $response->assertSessionHasErrors('email');
    }

    public function test_password_must_be_at_least_8_characters()
    {
        // パスワードを8文字未満で登録した場合、バリデーションエラーになるか確認
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        // passwordフィールドにエラーが入っているか確認
        $response->assertSessionHasErrors('password');
    }

    public function test_password_confirmation_must_match()
    {
        // パスワード確認が一致しない場合、バリデーションエラーになるか確認
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test2@example.com',
            'password' => 'password',
            'password_confirmation' => 'different',
        ]);

        // passwordフィールドにエラーが入っているか確認
        $response->assertSessionHasErrors('password');
    }

    public function test_password_is_required()
    {
        // パスワード未入力で会員登録した場合、バリデーションエラーになるか確認
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test3@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        // passwordフィールドにエラーが入っているか確認
        $response->assertSessionHasErrors('password');
    }

    public function test_user_can_register()
    {
        // 正しい内容で会員登録できるか確認
        $response = $this->post('/register', [
            'name' => '山田太郎',
            'email' => 'lasttest@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // エラーがないことを確認
        $response->assertSessionHasNoErrors();

        // 登録後のリダイレクト確認（ホームなど）
        $response->assertStatus(302);

        // usersテーブルに保存されたか確認
        $this->assertDatabaseHas('users', [
            'name' => '山田太郎',
            'email' => 'lasttest@example.com',
        ]);
    }
}
