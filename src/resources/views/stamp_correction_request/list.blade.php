@extends('layouts.app')

@section('content')

<div class="container">

    <h1>申請一覧</h1>

    {{-- ========================= --}}
    {{-- 承認待ち --}}
    {{-- ========================= --}}
    <h2>承認待ち</h2>

    <table border="1">
        <tr>
            <th>状態</th>
            <th>名前</th>
            <th>対象日時</th>
            <th>申請理由</th>
            <th>申請日時</th>
            <th>詳細</th>
        </tr>

        {{-- データがある場合 --}}
        @forelse ($pendingRequests as $request)
        <tr>
            {{-- 状態 --}}
            <td>{{ $request->status }}</td>

            {{-- 名前（userテーブル） --}}
            <td>{{ $request->user->name ?? '' }}</td>

            {{-- 対象日時（勤怠日 + 時間） --}}
            <td>
                {{ $request->attendance->date ?? '' }}
                {{ $request->start_time }}〜{{ $request->end_time }}
            </td>

            {{-- 理由 --}}
            <td>{{ $request->note }}</td>

            {{-- 申請日時 --}}
            <td>{{ $request->created_at }}</td>

            {{-- 詳細（あとでルーティングつける） --}}
            <td>
                <a href="#">詳細</a>
            </td>
        </tr>

        {{-- データがない場合 --}}
        @empty
        <tr>
            <td colspan="6">承認待ちの申請はありません</td>
        </tr>
        @endforelse
    </table>

    <br>

    {{-- ========================= --}}
    {{-- 承認済み --}}
    {{-- ========================= --}}
    <h2>承認済み</h2>

    <table border="1">
        <tr>
            <th>状態</th>
            <th>名前</th>
            <th>対象日時</th>
            <th>申請理由</th>
            <th>申請日時</th>
            <th>詳細</th>
        </tr>

        {{-- データがある場合 --}}
        @forelse ($approvedRequests as $request)
        <tr>
            <td>{{ $request->status }}</td>
            <td>{{ $request->user->name ?? '' }}</td>
            <td>
                {{ $request->attendance->date ?? '' }}
                {{ $request->start_time }}〜{{ $request->end_time }}
            </td>
            <td>{{ $request->note }}</td>
            <td>{{ $request->created_at }}</td>
            <td>
                <a href="#">詳細</a>
            </td>
        </tr>

        {{-- データがない場合 --}}
        @empty
        <tr>
            <td colspan="6">承認済みの申請はありません</td>
        </tr>
        @endforelse
    </table>

</div>

@endsection