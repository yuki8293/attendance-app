{{-- 勤怠詳細画面＿承認待ち勤怠画面 --}}

@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pending.css') }}">
@endsection

@section('content')

<h2 class="detail-title">勤怠詳細</h2>

<div class="detail-container">

    <div class="detail-row">
        <div class="label">名前</div>

        <div class="content">
            {{ $attendance->user->name }}
        </div>
    </div>

    <div class="detail-row">
        <div class="label">日付</div>

        <div class="content">
            {{ \Carbon\Carbon::parse($attendance->work_date)->format('Y/m/d') }}
        </div>
    </div>

    @php
    $start = $requestData->start_time ?? $attendance->start_time;
    $end = $requestData->end_time ?? $attendance->end_time;
    @endphp

    <div class="detail-row">
        <div class="label">出勤・退勤</div>

        <div class="time-group">

            <span class="time">
                {{ $start ? \Carbon\Carbon::parse($start)->format('H:i') : '' }}
            </span>

            <span>〜</span>

            <span class="time">
                {{ $end ? \Carbon\Carbon::parse($end)->format('H:i') : '' }}
            </span>

        </div>
    </div>

    @php
    $breakStart = $requestData->break_start ?? null;
    $breakEnd = $requestData->break_end ?? null;
    @endphp

    <div class="detail-row">
        <div class="label">休憩</div>

        <div class="time-group">

            <span class="time">
                {{ $breakStart ? \Carbon\Carbon::parse($breakStart)->format('H:i') : '' }}
            </span>

            <span>〜</span>

            <span class="time">
                {{ $breakEnd ? \Carbon\Carbon::parse($breakEnd)->format('H:i') : '' }}
            </span>

        </div>
    </div>

    <div class="detail-row">
        <div class="label">備考</div>

        <div class="content">
            {{ $attendance->note }}
        </div>
    </div>

</div>

{{-- ▼ 注意メッセージ --}}
<div class="notice-message">
    ・承認待ちのため修正は出来ません
</div>

@endsection