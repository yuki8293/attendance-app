<!-- 一般ユーザー用申請画面 -->

@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request.css') }}">
@endsection

@section('content')

<div class="request-container">

    <h1 class="request-title">申請一覧</h1>

    {{-- タブ --}}
    <div class="request-tabs">
        <button class="tab-button" onclick="showTab('pending')">承認待ち</button>
        <button class="tab-button" onclick="showTab('approved')">承認済み</button>
    </div>

    {{-- ========================= --}}
    {{-- 承認待ち --}}
    {{-- ========================= --}}
    <div id="pending" class="request-content">
        <h2 class="tab-title">承認待ち</h2>

        <table class="request-table">
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>

            @forelse ($pendingRequests as $request)
            <tr>
                <td>{{ $request->status }}</td>
                <td>{{ $request->user->name ?? '' }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($request->attendance->work_date)->format('Y/m/d') }}
                </td>
                <td>{{ $request->note }}</td>
                <td>{{ $request->created_at->format('Y/m/d') }}</td>
                <td><a class="detail-link" href="{{ route('attendance.pending', ['id' => $request->attendance->id]) }}">詳細</a></td>
            </tr>
            @empty
            <tr>
                <td colspan="6">承認待ちの申請はありません</td>
            </tr>
            @endforelse
        </table>
    </div>

    {{-- ========================= --}}
    {{-- 承認済み --}}
    {{-- ========================= --}}
    <div id="approved" class="request-content" style="display:none;">
        <h2 class="tab-title">承認済み</h2>

        <table class="request-table">
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>

            @forelse ($approvedRequests as $request)
            <tr>
                <td>{{ $request->status }}</td>
                <td>{{ $request->user->name ?? '' }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($request->attendance->work_date)->format('Y/m/d') }}
                </td>
                <td>{{ $request->note }}</td>
                <td>{{ $request->created_at->format('Y/m/d') }}</td>
                <td><a class="detail-link" href="{{ route('attendance.pending', ['id' => $request->attendance->id]) }}">詳細</a></td>
            </tr>
            @empty
            <tr>
                <td colspan=" 6">承認済みの申請はありません</td>
            </tr>
            @endforelse
        </table>
    </div>

</div>

{{-- これが切り替えの正体 --}}
<script>
    function showTab(tab) {
        document.getElementById('pending').style.display = 'none';
        document.getElementById('approved').style.display = 'none';

        document.getElementById(tab).style.display = 'block';
    }
</script>

@endsection