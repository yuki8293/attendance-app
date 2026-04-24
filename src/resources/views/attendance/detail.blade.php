<!-- 勤怠詳細画面(一般ユーザー) -->

@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')

<h2 class="detail-title">勤怠詳細</h2>

<div class="page">
    <div class="detail-container">

        <form id="edit-form" action="{{ route('stamp_correction_request.store') }}" method="POST">
            @csrf

            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

            <div class="row">
                <div class="label">名前</div>
                <div class="value">{{ $attendance->user->name }}</div>
            </div>

            <div class="row">
                <div class="label">日付</div>
                <div class="value">
                    {{ \Carbon\Carbon::parse($attendance->work_date)->format('Y/m/d') }}
                </div>
            </div>

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

            <div class="row">
                <div class="label">休憩2</div>
                <div class="value">
                    <input type="time" name="break_new_start">
                    〜
                    <input type="time" name="break_new_end">
                </div>
            </div>

            <div class="row">
                <div class="label">備考</div>
                <div class="value">
                    <textarea name="note">{{ $attendance->note ?? '' }}</textarea>
                </div>
            </div>

        </form>


        <div class="detail-actions">
            <button type="submit" form="edit-form">修正</button>
        </div>


</div>
</div>

@endsection