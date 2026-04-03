<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\BreakTime;

class Attendance extends Model
{
    use HasFactory;

    // 保存OKなカラム
    protected $fillable = [
        'user_id', // ユーザーID
        'work_date', // 勤務日
        'start_time', // 出勤時間
        'end_time', // 退勤時間
        'break_start', // 休憩開始
        'break_end', // 休憩終了
    ];

    // データの型変換
    protected $casts = [
        'work_date' => 'date', // 日付として扱う
        'start_time' => 'datetime', // 日時として扱う
        'end_time' => 'datetime', // 日時として扱う
    ];

    // この勤怠はどのユーザーのものか（多対1）
    public function user()
    {
        return $this->belongsTo(User::class);
        // belongsTo「この勤怠は一人のユーザーに属する」
    }

    // この勤怠に紐づく休憩一覧（1対多）
    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
        // 1つの勤怠に対して複数の休憩がある
    }
}
