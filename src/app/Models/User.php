<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// 勤怠モデルを使えるようにする
use App\Models\Attendance;

class User extends Authenticatable
{
    // 便利な機能をUserに追加
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // ユーザーに書き込ませてもOKな項目
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // 外に見せない項目
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // メール確認日時を日付として扱う
    ];

    // 「このユーザーは何回勤怠を記録したか」を簡単に取り出す
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
