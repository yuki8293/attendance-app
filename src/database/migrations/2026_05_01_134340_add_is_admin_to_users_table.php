<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAdminToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // usersテーブルに「管理者かどうか」を判定するカラムを追加
        Schema::table('users', function (Blueprint $table) {

            // true = 管理者 / false = 一般ユーザー
            $table->boolean('is_admin')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // migrationを取り消す（元に戻す）ときに実行される処理
        Schema::table('users', function (Blueprint $table) {

            // 追加した is_admin カラムを削除する
            $table->dropColumn('is_admin');
        });
    }
}
