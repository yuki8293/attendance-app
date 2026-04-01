<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attendance;

class BreakTime extends Model
{
    use HasFactory;

    // テーブル名が違うので指定
    protected $table = 'breaks';

    // この項目はまとめてデータ登録してOK！
    protected $fillable = [
        'attendance_id',
        'start_time',
        'end_time',
    ];

    // このデータは1つの勤怠に属している(多対１)
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
