@extends('layouts.admin')

@section('content')

<h1>管理者：勤怠一覧</h1>

@foreach ($attendances as $attendance)
<p>{{ $attendance->user->name ?? '名前なし' }}</p>
@endforeach

@endsection
