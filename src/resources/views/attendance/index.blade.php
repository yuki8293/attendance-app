@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

@php
$attendance = auth()->user()
->attendances()
->whereDate('work_date', now())
->first();

// 休憩中判定
$onBreak = false;

if ($attendance) {
$onBreak = \App\Models\BreakTime::where('attendance_id', $attendance->id)
->whereNull('end_time')
->exists();
}

// ステータス初期値
$status = '勤務外';

if ($attendance) {
if ($attendance->end_time) {
$status = '退勤済';
} elseif ($onBreak) {
$status = '休憩中';
} elseif ($attendance->start_time) {
$status = '出勤中';
}
}
@endphp

<div class="attendance-main">

    {{-- ステータス表示 --}}
    <h2>{{ $status }}</h2>

    {{-- 日付 --}}
    <p>{{ \Carbon\Carbon::now()->locale('ja')->isoFormat('YYYY年M月D日（ddd）') }}</p>

    {{-- 時刻 --}}
    <p>{{ now()->format('H:i') }}</p>

    {{-- 出勤前のみ出勤ボタン表示 --}}
    @if(!$attendance || !$attendance->start_time)
    <form method="POST" action="{{ route('attendance.store') }}">
        @csrf
        <button type="submit" name="action" value="start" class="attendance-button">
            出勤
        </button>
    </form>

    @elseif($status === '出勤中')

    <form method="POST" action="{{ route('attendance.store') }}">
        @csrf
        <button type="submit" name="action" value="break_start" class="attendance-button">
            休憩入
        </button>
    </form>

    <form method="POST" action="{{ route('attendance.store') }}">
        @csrf
        <button type="submit" name="action" value="end" class="attendance-button">
            退勤
        </button>
    </form>

    @elseif($status === '休憩中')

    <form method="POST" action="{{ route('attendance.store') }}">
        @csrf
        <button type="submit" name="action" value="break_end" class="attendance-button">
            休憩戻
        </button>
    </form>
    @endif

</div>

@endsection