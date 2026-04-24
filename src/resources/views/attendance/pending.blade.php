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

<div>
    出勤：
    {{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '' }}
    〜
    {{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}
</div>

@foreach($attendance->breaks->take(2) as $break)
<div>
    休憩：
    {{ \Carbon\Carbon::parse($break->start_time)->format('H:i') }}
    〜
    {{ \Carbon\Carbon::parse($break->end_time)->format('H:i') }}
</div>
@endforeach

<div>
    備考：{{ $attendance->note }}
</div>

{{-- ▼ 注意メッセージ --}}
<div class="notice-message">
    ・承認待ちのため修正は出来ません
</div>

@endsection
