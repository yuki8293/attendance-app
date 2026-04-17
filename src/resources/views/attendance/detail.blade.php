<!-- 勤怠詳細画面　-->

@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')

<h2>勤怠詳細</h2>

<div class="page">
    <div class="detail-container">

        {{-- ▼ 更新フォーム --}}
        <form action="{{ route('stamp_correction_request.store') }}" method="POST">
            @csrf

            {{-- 勤怠ID（超重要） --}}
            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

            {{-- 名前（表示だけ） --}}
            <div>
                名前：{{ $attendance->user->name }}
            </div>

            {{-- 日付（表示だけ） --}}
            <div>
                日付：{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y/m/d') }}
            </div>

            {{-- 出勤・退勤 --}}
            <div>
                出勤・退勤：
                <input
                    type="time"
                    name="start_time"
                    value="{{ optional($attendance->start_time)->format('H:i') }}">
                〜
                <input
                    type="time"
                    name="end_time"
                    value="{{ optional($attendance->end_time)->format('H:i') }}">
            </div>

            {{-- 休憩（入力） --}}
            @foreach($attendance->breaks->take(2) as $index => $break)
            <div class="row">
                <div class="label">休憩{{ $loop->iteration }}</div>
                <div class="value">

                    <input
                        type="time"
                        name="breaks[{{ $index }}][start]"
                        value="{{ optional($break->start_time)->format('H:i') }}">

                    〜

                    <input
                        type="time"
                        name="breaks[{{ $index }}][end]"
                        value="{{ optional($break->end_time)->format('H:i') }}">

                </div>
            </div>
            @endforeach

            {{-- 休憩（追加） --}}
            <div class="row">
                <div class="label">休憩追加</div>
                <div class="value">
                    <input type="time" name="break_new_start">
                    〜
                    <input type="time" name="break_new_end">
                </div>
            </div>

            {{-- 備考 --}}
            <div>
                備考：
                <textarea name="note">{{ $attendance->note ?? '' }}</textarea>
            </div>

            {{-- 修正ボタン --}}
            <button type="submit">修正</button>
        </form>

    </div>
</div>

@endsection