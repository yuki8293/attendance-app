<!-- 管理者用の勤怠詳細画面 -->

@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')

<h2 class="detail-title">勤怠詳細</h2>

<div class="page">

    <form action="{{ route('stamp_correction_request.store') }}" method="POST">
        @csrf

        {{-- 勤怠ID --}}
        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

        <div class="detail-container">

            {{-- 名前 --}}
            <div class="row">
                <div class="label">名前</div>
                <div class="value">{{ $attendance->user->name }}</div>
            </div>

            {{-- 日付 --}}
            <div class="row">
                <div class="label">日付</div>
                <div class="value">
                    {{ \Carbon\Carbon::parse($attendance->work_date)->format('Y/m/d') }}
                </div>
            </div>

            {{-- 出勤・退勤 --}}
            <div class="row">
                <div class="label">出勤・退勤</div>
                <div class="value">
                    <input type="time" name="start_time"
                        value="{{ optional($attendance->start_time)->format('H:i') }}">
                    〜
                    <input type="time" name="end_time"
                        value="{{ optional($attendance->end_time)->format('H:i') }}">
                </div>
            </div>

            {{-- 休憩 --}}
            @foreach($attendance->breaks->take(2) as $index => $break)
            <div class="row">
                <div class="label">休憩{{ $loop->iteration }}</div>
                <div class="value">
                    <input type="time" name="breaks[{{ $index }}][start]"
                        value="{{ optional($break->start_time)->format('H:i') }}">
                    〜
                    <input type="time" name="breaks[{{ $index }}][end]"
                        value="{{ optional($break->end_time)->format('H:i') }}">
                </div>
            </div>
            @endforeach

            {{-- 休憩追加 --}}
            <div class="row">
                <div class="label">休憩2</div>
                <div class="value">
                    <input type="time" name="break_new_start">
                    〜
                    <input type="time" name="break_new_end">
                </div>
            </div>

            {{-- 備考 --}}
            <div class="row">
                <div class="label">備考</div>
                <div class="value">
                    <textarea name="note">{{ old('note', $attendance->note) }}</textarea>
                </div>
            </div>

        </div>

        {{-- ボタン --}}
        <div class="detail-actions">
            <button type="submit">修正</button>
        </div>

    </form>

</div>

@endsection