@extends('layouts.admin')

@section('content')
<h1>勤怠詳細（管理者）</h1>

<p>名前：{{ $attendance->user->name }}</p>
<p>日付：{{ $attendance->date }}</p>
<p>出勤：{{ $attendance->start_time }}</p>
<p>退勤：{{ $attendance->end_time }}</p>
@endsection