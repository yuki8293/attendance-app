<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminFactory extends Factory
{

    // このFactoryが対象とするモデル（Adminモデル用）
    protected $model = Admin::class;

    // テスト用の管理者データを生成する
    public function definition()
    {
        return [
            'name' => 'テスト管理者',

            // 重複しないメールアドレスを自動生成
            'email' => $this->faker->unique()->safeEmail(),

            // パスワードはハッシュ化して保存（ログインテスト対応）
            'password' => Hash::make('password'),
        ];
    }
}
