<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Attendance;

class AttendanceRequest extends Model
{
    use HasFactory;

    // この6項目だけまとめて保存してOK
    protected $fillable = [
        'user_id',
        'attendance_id',
        'start_time',
        'end_time',
        'note',
        'status',
    ];

    //申請が多/ユーザー、勤怠が１
    // この申請をしたユーザー（多対1）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //この申請が対象の勤怠（多対1）
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
