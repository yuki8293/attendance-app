<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreaksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('breaks', function (Blueprint $table) {
            // 主キー(自動で番号が振られる)
            $table->id();
            // attendancesテーブルと紐づけるID(外部キー)
            $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
            // 休憩開始時間
            $table->time('start_time');
            //　休憩終了時間
            $table->time('end_time')->nullable();
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
        Schema::dropIfExists('breaks');
    }
}
