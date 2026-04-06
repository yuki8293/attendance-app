@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')

<h2>勤怠詳細</h2>

<div class="page">
    <div class="detail-container">

        {{-- ▼ 更新フォーム --}}
        <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- エラー表示 --}}
            @if ($errors->any())
            <div>
                {{ $errors->first() }}
            </div>
            @endif

            {{-- 名前（表示のみ） --}}
            <div class="row">
                <div class="label">名前</div>
                <div class="value">
                    {{ $attendance->user->name }}
                </div>
            </div>

            {{-- 日付（表示のみ） --}}
            <div class="row">
                <div class="label">日付</div>
                <div class="value">
                    {{ \Carbon\Carbon::parse($attendance->work_date)->format('Y/m/d') }}
                </div>
            </div>

            {{-- 出勤・退勤（入力） --}}
            <div class="row">
                <div class="label">出勤・退勤</div>
                <div class="value">
                    <input
                        type="time"
                        name="clock_in"
                        value="{{ optional($attendance->start_time)->format('H:i') }}">
                    〜
                    <input
                        type="time"
                        name="clock_out"
                        value="{{ optional($attendance->end_time)->format('H:i') }}">
                </div>
            </div>

            {{-- 休憩1（入力） --}}
            @foreach($attendance->breaks as $index => $break)
            <div class="row">
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
                @endforeach

                {{-- 休憩2（入力） --}}
                <div class="row">
                    <div class="label">休憩2</div>
                    <div class="value">
                        <input type="time" name="break_new_start">
                        〜
                        <input type="time" name="break_new_end">
                    </div>
                </div>

                {{-- 備考（入力） --}}
                <div class="row">
                    <div class="label">備考</div>
                    <div class="value">
                        <textarea name="note">{{ $attendance->note ?? '' }}</textarea>
                    </div>
                </div>

                {{-- 修正ボタン --}}
                <div class="row">
                    <button type="submit">修正</button>
                </div>

        </form>

    </div>
</div>

@endsection