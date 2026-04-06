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
            // 出勤時間（必須）
            'clock_in' => ['required'],

            // 退勤時間（必須 + 出勤時間より後であること）
            'clock_out' => ['required', 'after:clock_in'],

            // 休憩（配列対応）
            'breaks.*.start' => ['nullable', 'after_or_equal:clock_in'],
            'breaks.*.end' => ['nullable', 'before_or_equal:clock_out'],

            // 備考（必須）
            'note' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です',

            'breaks.*.start.after_or_equal' => '休憩時間が勤務時間外です',
            'breaks.*.end.before_or_equal' => '休憩時間が勤務時間外です',

            'note.required' => '備考を記入してください',
        ];
    }
}
