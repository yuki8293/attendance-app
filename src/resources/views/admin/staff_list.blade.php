{{-- 管理者用のスタッフ一覧画面 --}}

@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="admin-staff-list">

    <h1>スタッフ一覧</h1>

    <table>
        <tr>
            <th>名前</th>
            <th>メール</th>
            <th>月次勤怠</th>
        </tr>

        @foreach ($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                <a href="{{ route('admin.staff.attendance', $user->id) }}">
                    詳細
                </a>
            </td>
        </tr>
        @endforeach
    </table>

</div>
@endsection
