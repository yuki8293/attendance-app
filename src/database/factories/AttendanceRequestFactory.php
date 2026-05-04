<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Attendance;

class AttendanceRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // ユーザーID（関連）
            'user_id' => User::factory(),

            // 勤怠ID（関連）
            'attendance_id' => Attendance::factory(),

            // 適当な時間
            'start_time' => '09:00',
            'end_time' => '18:00',

            // 備考
            'note' => $this->faker->sentence(),

            // ステータス
            'status' => '承認待ち',
        ];
    }
}
