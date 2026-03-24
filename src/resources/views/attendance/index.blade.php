@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

<div class="attendance-header">
    <div class="attendance-logo">
        <img src="{{ asset('images/coachtech-logo.png') }}" alt="コーチテック">
    </div>
    <div class="attendance-menu">
        <a href="{{ route('attendance.index') }}">勤怠</a>
        <a href="{{ route('attendance.list') }}">退勤一覧</a>
        <a href="{{ route('stamp_request.list') }}">申請</a>
        <a href="{{ route('logout') }}">ログアウト</a>
    </div>
</div>

<div class="attendance-main">
    <h2>勤務外</h2>
    <p>{{ now()->format('Y-m-d') }}</p>
    <p>{{ now()->format('H:i') }}</p>

    @php
    $attendance = auth()->user()->attendances()->whereDate('created_at', now())->first();
    @endphp

    @if(!$attendance || !$attendance->clock_in)
    <form method="POST" action="{{ route('attendance.store') }}">
        @csrf
        <button type="submit" class="attendance-button">出勤</button>
    </form>
    @endif
</div>

@endsection