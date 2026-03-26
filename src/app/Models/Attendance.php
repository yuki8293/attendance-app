<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\BreakTime;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'start_time',
        'end_time',
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
