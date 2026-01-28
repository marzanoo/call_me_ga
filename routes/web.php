<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Report\AdminReportController;
use App\Http\Controllers\Report\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login.show');
        Route::post('/login', 'login')->name('login');
        Route::get('/register', 'showRegister')->name('register.show');
        Route::post('/register', 'register')->name('register');
        Route::middleware('superapp.auth')->post('/logout', 'logout')->name('logout');
    });
});

Route::middleware('superapp.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('reports')->controller(ReportController::class)->group(function () {
        Route::get('/', 'index')->name('reports.index');
        Route::post('/add', 'store')->name('reports.store');
    });
    Route::prefix('history')->controller(HistoryController::class)->group(function () {
        Route::get('/', 'index')->name('history.index');
        Route::get('/{id}', 'show')->name('history.show');
    });
    Route::prefix('admin')->group(function () {
        Route::prefix('reports')->controller(AdminReportController::class)->group(function () {
            Route::get('/', 'index')->name('admin.reports.index');
            Route::get('/finish', 'finishIndex')->name('admin.reports.finish.index');
            Route::get('/waiting', 'waitingIndex')->name('admin.reports.waiting.index');
            Route::get('/processed', 'processedIndex')->name('admin.reports.processed.index');
            Route::get('/processed/{id}', 'processedShow')->name('admin.reports.processed.show');
            Route::post('/processed/{id}/update-status', 'processedUpdateStatus')->name('admin.reports.processed.update-status');
            Route::get('/waiting/{id}', 'waitingShow')->name('admin.reports.waiting.show');
            Route::post('/waiting/{id}/update-status', 'waitingUpdateStatus')->name('admin.reports.waiting.update-status');
            Route::get('/finish/done', 'finishDoneIndex')->name('admin.reports.finish.done.index');
            Route::get('/finish/declined', 'finishDeclinedIndex')->name('admin.reports.finish.declined.index');
            Route::get('/finish/done/{id}', 'finishDoneShow')->name('admin.reports.finish.done.show');
            Route::get('/finish/declined/{id}', 'finishDeclinedShow')->name('admin.reports.finish.declined.show');
        });
    });
});
