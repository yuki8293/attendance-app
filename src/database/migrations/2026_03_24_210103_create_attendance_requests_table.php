<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_requests', function (Blueprint $table) {
            // 主キー
            $table->id();

            // どのユーザーが申請したか
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // どの勤怠の修正か
            $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
            // 修正後の出勤時間
            $table->time('start_time');
            // 修正後の退勤時間
            $table->time('end_time');
            // 備考(理由)
            $table->text('note')->nullable();
            // 認証待ち/認証済み
            $table->string('status');
            // 作成日時・更新日時
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_requests');
    }
}
