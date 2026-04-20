{{-- 管理者用の申請一覧画面 --}}

@extends('layouts.admin')

@section('content')

<div class="container">

    <h1>申請一覧</h1>

    {{-- タブ --}}
    <div class="tabs">
        <button onclick="showTab('pending')">承認待ち</button>
        <button onclick="showTab('approved')">承認済み</button>
    </div>

    {{-- ========================= --}}
    {{-- 承認待ち --}}
    {{-- ========================= --}}
    <div id="pending">
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

            @forelse ($pendingRequests as $request)
            <tr>
                <td>{{ $request->status }}</td>
                <td>{{ $request->user->name ?? '' }}</td>
                <td>
                    {{ $request->attendance->work_date ?? '' }}
                    {{ $request->start_time }}〜{{ $request->end_time }}
                </td>
                <td>{{ $request->note }}</td>
                <td>{{ $request->created_at }}</td>
                <td><a href="{{ route('admin.stamp_request.approve', $request->id) }}">詳細</a></td>
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
    <div id="approved" style="display:none;">
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
                <td><a href="{{ route('admin.stamp_request.approve', $request->id) }}">詳細</a></td>
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