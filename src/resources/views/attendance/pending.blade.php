{{-- 勤怠詳細画面＿承認待ち勤怠画面 --}}

@extends('layouts.app')

@section('content')

<h2>勤怠詳細</h2>

<div>
    名前：{{ $attendance->user->name }}
</div>

<div>
    日付：{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y/m/d') }}
</div>

@php
$start = $requestData->start_time ?? $attendance->start_time;
$end = $requestData->end_time ?? $attendance->end_time;
@endphp

<div>
    出勤：
    {{ $start ? \Carbon\Carbon::parse($start)->format('H:i') : '' }}
    〜
    {{ $end ? \Carbon\Carbon::parse($end)->format('H:i') : '' }}
</div>

@php
$breakStart = $requestData->break_start ?? null;
$breakEnd = $requestData->break_end ?? null;
@endphp

<div>
    休憩：
    {{ $breakStart ? \Carbon\Carbon::parse($breakStart)->format('H:i') : '' }}
    〜
    {{ $breakEnd ? \Carbon\Carbon::parse($breakEnd)->format('H:i') : '' }}
</div>

<div>
    備考：{{ $attendance->note }}
</div>

{{-- ▼ 注意メッセージ --}}
<div class="notice-message">
    ・承認待ちのため修正は出来ません
</div>

@endsection