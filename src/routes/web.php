<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Controllers\Admin\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// トップ → 勤怠へリダイレクト
Route::get('/', function () {
    return redirect('/attendance');
});

// ログイン後のみ一般ユーザー
Route::middleware('auth')->group(function () {
    // 勤怠登録画面
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    // 勤怠登録処理
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    // 勤怠一覧画面
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    // 勤怠詳細画面
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail'])->name('attendance.detail');
    // 勤怠更新ルート
    Route::put('/attendance/{id}', [AttendanceController::class, 'update'])
        ->name('attendance.update');
    // 承認待ち画面へのルート
    Route::get('/attendance/{id}/pending', [AttendanceController::class, 'pending'])
        ->name('attendance.pending');

    // 申請一覧画面
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])->name('stamp_correction_request.list');

    // 申請保存
    Route::post('/stamp_correction_request/store', [StampCorrectionRequestController::class, 'store'])
        ->name('stamp_correction_request.store');
});

// 管理者ログイン
Route::prefix('admin')->group(function () {
    // 管理者ログイン画面
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    // 管理者ログイン処理
    Route::post('/login', [LoginController::class, 'login']);
});

// 管理者ログイン後のみ
Route::prefix('admin')->middleware('auth:admin')->group(function () {

    // 管理者用ログアウト
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('admin.logout');

    // 管理者勤怠一覧
    Route::get('/attendance/list', [AttendanceController::class, 'adminList'])->name('admin.attendance.list');

    // スタッフ一覧
    Route::get('/staff/list', [StaffController::class, 'list'])->name('admin.staff.list');

    // スタッフ別勤怠一覧
    Route::get('/attendance/staff/{id}', [AttendanceController::class, 'staffAttendance'])->name('admin.staff.attendance');

    // スタッフ別勤怠CSV出力
    Route::get(
        '/attendance/staff/{id}/csv/{year}/{month}',
        [AttendanceController::class, 'exportCsv']
    )->name('admin.staff.attendance.csv');

    // 修正申請一覧
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'list'])
        ->name('admin.stamp_request.list');

    // 修正申請承認画面
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [StampCorrectionRequestController::class, 'approve'])->name('admin.stamp_request.approve');

    // 承認処理
    Route::post(
        '/stamp_correction_request/approve/{id}',
        [StampCorrectionRequestController::class, 'updateStatus']
    )
        ->name('admin.stamp_request.updateStatus');

    // 管理者勤怠詳細
    Route::get('/attendance/{id}', [AttendanceController::class, 'adminDetail'])->name('admin.attendance.detail');
});
