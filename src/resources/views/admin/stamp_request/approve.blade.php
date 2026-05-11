{{-- 管理者用の修正申請承認画面 --}}

@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')

<h2 class="detail-title">勤怠詳細</h2>

<div class="detail-page">
    <div class="detail-card admin-detail">

        {{-- 名前 --}}
        <div class="row">
            <div class="label">名前</div>
            <div class="value">
                {{ $attendance_correct_request->user->name }}
            </div>
        </div>

        {{-- 日付 --}}
        <div class="row">
            <div class="label">日付</div>
            <div class="value">
                {{ \Carbon\Carbon::parse($attendance_correct_request->attendance->work_date)->format('Y/m/d') }}
            </div>
        </div>

        {{-- 出勤・退勤 --}}
        <div class="row">
            <div class="label">出勤・退勤</div>
            <div class="value">
                {{ \Carbon\Carbon::parse($attendance_correct_request->start_time)->format('H:i') }}
                〜
                {{ \Carbon\Carbon::parse($attendance_correct_request->end_time)->format('H:i') }}
            </div>
        </div>

        {{-- 休憩 --}}
        <div class="row">
            <div class="label">休憩</div>

            <div class="value">

                @if($attendance_correct_request->break_start && $attendance_correct_request->break_end)

                {{ \Carbon\Carbon::parse($attendance_correct_request->break_start)->format('H:i') }}
                〜
                {{ \Carbon\Carbon::parse($attendance_correct_request->break_end)->format('H:i') }}

                @elseif(isset($breaks[0]))

                {{ \Carbon\Carbon::parse($breaks[0]->start_time)->format('H:i') }}
                〜
                {{ \Carbon\Carbon::parse($breaks[0]->end_time)->format('H:i') }}

                @else
                -
                @endif

            </div>
        </div>

        {{-- 休憩2 --}}
        <div class="row">
            <div class="label">休憩2</div>
            <div class="value">
                {{ isset($breaks[1])
            ? \Carbon\Carbon::parse($breaks[1]->start_time)->format('H:i') . ' 〜 ' . \Carbon\Carbon::parse($breaks[1]->end_time)->format('H:i')
            : '-' }}
            </div>
        </div>


        {{-- 備考 --}}
        <div class="row">
            <div class="label">備考</div>
            <div class="value">
                {{ $attendance_correct_request->note }}
            </div>
        </div>

    </div>

    {{-- ボタン --}}
    <div class="detail-actions">

        @if($attendance_correct_request->status === '承認済み')
        <button class="detail-button" disabled>承認済み</button>
        @else
        <button id="approve-btn" class="detail-button">承認</button>
        @endif

    </div>

    @endsection

    @section('scripts')
    <script>
        const btn = document.getElementById('approve-btn');

        if (btn) {
            btn.addEventListener('click', function() {

                fetch("{{ route('admin.stamp_request.updateStatus', $attendance_correct_request->id) }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json"
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        btn.textContent = '承認済み';
                        btn.disabled = true;
                    });

            });
        }
    </script>
    @endsection