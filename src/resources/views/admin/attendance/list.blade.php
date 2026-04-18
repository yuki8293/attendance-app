{{-- 管理者用の勤怠一覧画面 --}}

@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')

<div class="admin-attendance-list">

    {{-- タイトル --}}
    <h1 class="admin-attendance-list__title">
        {{ $date->format('Y年m月d日') }}の勤怠
    </h1>

    <div class="admin-attendance-list__nav">

        @php
        use Carbon\Carbon;

        $currentDate = $date;
        $prevDate = $currentDate->copy()->subDay()->format('Y-m-d');
        $nextDate = $currentDate->copy()->addDay()->format('Y-m-d');
        @endphp

        {{-- 前日 --}}
        <a href="{{ route('admin.attendance.list', ['date' => $prevDate]) }}"
            class="admin-attendance-list__nav-btn">
            ← 前日
        </a>

        {{-- 日付 --}}
        <div class="admin-attendance-list__date">
            📅 {{ $currentDate->format('Y年m月d日') }}
        </div>

        {{-- 翌日 --}}
        <a href="{{ route('admin.attendance.list', ['date' => $nextDate]) }}"
            class="admin-attendance-list__nav-btn">
            翌日 →
        </a>

    </div>

    {{-- テーブル --}}
    <table class="admin-attendance-list__table">

        <thead class="admin-attendance-list__thead">
            <tr class="admin-attendance-list__row">
                <th class="admin-attendance-list__header">日付</th>
                <th class="admin-attendance-list__header">名前</th>
                <th class="admin-attendance-list__header">出勤</th>
                <th class="admin-attendance-list__header">退勤</th>
                <th class="admin-attendance-list__header">詳細</th>
            </tr>
        </thead>

        <tbody class="admin-attendance-list__body">
            @foreach ($attendances as $attendance)
            <tr class="admin-attendance-list__row">

                {{-- 日付 --}}
                <td class="admin-attendance-list__data">
                    {{ \Carbon\Carbon::parse($attendance->work_date)->format('m/d') }}
                </td>

                {{-- 名前 --}}
                <td class="admin-attendance-list__data">
                    {{ $attendance->user->name ?? '名前なし' }}
                </td>

                {{-- 出勤 --}}
                <td class="admin-attendance-list__data">
                    {{ optional($attendance->start_time)->format('H:i') }}
                </td>

                {{-- 退勤 --}}
                <td class="admin-attendance-list__data">
                    {{ optional($attendance->end_time)->format('H:i') }}
                </td>

                {{-- 詳細 --}}
                <td class="admin-attendance-list__data">
                    <a href="{{ route('admin.attendance.detail', $attendance->id) }}"
                        class="admin-attendance-list__detail-link">
                        詳細
                    </a>
                </td>

            </tr>
            @endforeach
        </tbody>

    </table>

</div>

@endsection