{{-- 管理者用のスタッフ一覧画面 --}}

@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="admin-staff-list">

    <h1 class="page-title">スタッフ一覧</h1>

    <div class="table-wrapper">

        <table class="staff-table">

            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>

            @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>

                <td>{{ $user->email }}</td>

                <td>
                    <a
                        class="detail-link"
                        href="{{ route('admin.staff.attendance', $user->id) }}">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach

        </table>

    </div>

</div>

@endsection