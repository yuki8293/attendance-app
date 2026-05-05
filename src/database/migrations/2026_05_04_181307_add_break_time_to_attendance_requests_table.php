<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBreakTimeToAttendanceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_requests', function (Blueprint $table) {
            // 休憩開始時間を追加（NULL OK）
            $table->time('break_start')->nullable();

            // 休憩終了時間を追加（NULL OK）
            $table->time('break_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_requests', function (Blueprint $table) {
            // rollback時に削除
            $table->dropColumn(['break_start', 'break_end']);
        });
    }
}
