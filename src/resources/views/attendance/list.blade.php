<!-- 勤怠一覧画面　-->

@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="attendance-list">

    {{-- タイトル --}}
    <h2 class="attendance-list__title">勤怠一覧</h2>

    @php
    use Carbon\Carbon;

    // 表示中の月をCarbonで扱えるように変換
    $currentMonth = Carbon::createFromFormat('Y-m', $month);

    // 前月（1ヶ月前）を取得してURL用のフォーマットに変換
    $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');

    // 翌月（1ヶ月後）を取得してURL用のフォーマットに変換
    $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');
    @endphp

    {{-- 月ナビゲーション --}}
    <div class="attendance-list__nav">

        {{-- 先月 --}}
        <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}" class="attendance-list__nav-btn">
            ← 先月
        </a>

        {{-- 現在の月 --}}
        <div class="attendance-list__month">
            📅 {{ $currentMonth->format('Y年m月') }}
        </div>

        {{-- 翌月 --}}
        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="attendance-list__nav-btn">
            翌月 →
        </a>

    </div>

    {{-- テーブル --}}
    <table class="attendance-list__table">

        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($attendances as $attendance)
            <tr>

                {{-- 日付 --}}
                <td>
                    {{ \Carbon\Carbon::parse($attendance->work_date)->format('m/d') }}
                </td>

                {{-- 出勤 --}}
                <td>
                    {{ optional($attendance->start_time)->format('H:i') }}
                </td>

                {{-- 退勤 --}}
                <td>
                    {{ optional($attendance->end_time)->format('H:i') }}
                </td>

                {{-- 休憩（仮：あとで計算） --}}
                <td>
                    @php
                    // 休憩時間の合計を入れる変数
                    $breakMinutes = 0;

                    // この勤怠に紐づく全ての休憩を一つずつ取り出す
                    foreach ($attendance->breaks as $break) {
                    // 休憩開始・終了の両方がある場合のみ計算する
                    if ($break->start_time && $break->end_time) {

                    // 開始時間をCarbon（時間のデータ+機能）に変換
                    $start = \Carbon\Carbon::parse($break->start_time);

                    // 終了時間をCarbonに変換
                    $end = \Carbon\Carbon::parse($break->end_time);

                    // 終了 - 開始 = 休憩時間（分
                    $breakMinutes += $end->diffInMinutes($start);
                    }
                    }
                    @endphp

                    {{-- 分 → 時間:分 に変換して表示 --}}
                    {{ floor($breakMinutes / 60) }}:{{ str_pad($breakMinutes % 60, 2, '0', STR_PAD_LEFT) }}
                </td>

                {{-- 合計（仮：あとで計算） --}}
                <td>
                    @php

                    // 勤務時間（分）を入れる変数
                    $workMinutes = 0;

                    // 出勤と退勤の両方がある場合のみ計算
                    if ($attendance->start_time && $attendance->end_time) {

                    // 出勤時間をCarbonに変換
                    $start = \Carbon\Carbon::parse($attendance->start_time);

                    // 退勤時間をCarbonに変換
                    $end = \Carbon\Carbon::parse($attendance->end_time);

                    // 勤務時間（分） = 退勤 - 出勤 - 休憩
                    $workMinutes = $end->diffInMinutes($start) - $breakMinutes;
                    }
                    @endphp

                    {{-- 分 → 時間:分 に変換して表示 --}}
                    {{ floor($workMinutes / 60) }}:{{ str_pad($workMinutes % 60, 2, '0', STR_PAD_LEFT) }}
                </td>

                {{-- 詳細 --}}
                <td>
                    <a href="{{ route('attendance.detail', $attendance->id) }}">
                        詳細
                    </a>
                </td>

            </tr>
            @endforeach
        </tbody>

    </table>

</div>
@endsection