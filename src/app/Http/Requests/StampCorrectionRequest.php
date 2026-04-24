<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StampCorrectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_time' => ['required'],
            'end_time'   => ['required', 'after:start_time'],

            // 休憩（配列対応）
            'breaks.*.start' => ['nullable', 'after_or_equal:start_time'],
            'breaks.*.end'   => ['nullable', 'before_or_equal:end_time'],

            // 備考（必須）
            'note' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'end_time.after' => '出勤時間もしくは退勤時間が不適切な値です',

            'breaks.*.start.after_or_equal' => '休憩時間が勤務時間外です',
            'breaks.*.end.before_or_equal' => '休憩時間が勤務時間外です',

            'note.required' => '備考を記入してください',
        ];
    }
}
