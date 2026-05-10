{{-- 管理者のスタッフ別勤怠一覧画面 --}}

@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')

<div class="admin-staff-attendance">

    {{-- タイトル --}}
    <h1 class="page-title">
        {{ $user->name }}さんの勤怠
    </h1>

    {{-- 月移動 --}}
    <div class="month-nav">

        {{-- 前月 --}}
        <a
            class="month-link"
            href="{{ route('admin.staff.attendance', [
                'id' => $user->id,
                'year' => \Carbon\Carbon::create($year, $month)->subMonth()->year,
                'month' => \Carbon\Carbon::create($year, $month)->subMonth()->month
            ]) }}">
            ← 前月
        </a>

        {{-- 月表示 --}}
        <div class="current-month">
            📅 {{ $year }}/{{ sprintf('%02d', $month) }}
        </div>

        {{-- 翌月 --}}
        <a
            class="month-link"
            href="{{ route('admin.staff.attendance', [
                'id' => $user->id,
                'year' => \Carbon\Carbon::create($year, $month)->addMonth()->year,
                'month' => \Carbon\Carbon::create($year, $month)->addMonth()->month
            ]) }}">
            翌月 →
        </a>

    </div>

    {{-- テーブル --}}
    <div class="table-wrapper">

        <table class="attendance-table">

            <tr>

                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>

            </tr>

            @foreach ($attendances as $attendance)
            <tr>

                {{-- 日付 --}}
                <td class="date">
                    {{ \Carbon\Carbon::parse($attendance->work_date)->format('m/d') }}
                </td>

                {{-- 出勤 --}}
                <td class="start-time">
                    {{ optional($attendance->start_time)->format('H:i') }}
                </td>

                {{-- 退勤 --}}
                <td class="end-time">
                    {{ optional($attendance->end_time)->format('H:i') }}
                </td>

                {{-- 休憩 --}}
                <td class="break-time">
                    --
                </td>

                {{-- 合計 --}}
                <td class="total-time">
                    --
                </td>

                {{-- 詳細 --}}
                <td class="detail">
                    <a
                        class="detail-link"
                        href="{{ route('admin.attendance.detail', $attendance->id) }}">
                        詳細
                    </a>
                </td>

            </tr>
            @endforeach

        </table>

    </div>
    <div class="csv-button-area">

        <a
            class="csv-button"
            href="{{ route('admin.staff.attendance.csv', [$user->id, $year, $month]) }}">
            CSV出力
        </a>

    </div>
</div>

@endsection
