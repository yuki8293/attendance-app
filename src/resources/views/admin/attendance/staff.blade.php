{{-- 管理者のスタッフ別勤怠一覧画面 --}}

@extends('layouts.admin')

@section('content')
<div class="admin-staff-attendance">

    <h1>
        {{ $user->name }}さんの{{ $year }}年{{ $month }}月の勤怠
    </h1>

    <table>
        <tr>
            <th>日付</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>詳細</th>
        </tr>

        @foreach ($attendances as $attendance)
        <tr>
            <td>
                {{ \Carbon\Carbon::parse($attendance->work_date)->format('m/d') }}
            </td>
            <td>
                {{ optional($attendance->start_time)->format('H:i') }}
            </td>
            <td>
                {{ optional($attendance->end_time)->format('H:i') }}
            </td>
            <td>
                <a href="{{ route('admin.attendance.detail', $attendance->id) }}">
                    詳細
                </a>
            </td>
        </tr>
        @endforeach

    </table>

    <a href="{{ route('admin.staff.attendance.csv', [$user->id, $year, $month]) }}">
        CSV出力
    </a>

</div>
@endsection