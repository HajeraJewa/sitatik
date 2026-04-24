<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\DataSourceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PerangkatDaerahController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\LandingController;

// Jangan lupa buat controller ini atau arahkan ke yang sesuai
use Illuminate\Support\Facades\Route;

//  Landing Page
Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/opd', [DashboardController::class, 'filterOpd']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Manajemen Pengguna (User/Operator)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/users/{id}', [UserController::class, 'update'])->name('users.update');
    // PERBAIKAN: Arahkan ke UserController, bukan DashboardController
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Manajemen Rekomendasi
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
    Route::post('/recommendations', [RecommendationController::class, 'store'])->name('recommendations.store');
    Route::patch('/recommendations/{id}/{status}', [RecommendationController::class, 'updateStatus'])->name('recommendations.status');
    Route::patch('/recommendations/{id}', [RecommendationController::class, 'update'])->name('recommendations.update');
    Route::get('/recommendations/{id}/pdf', [RecommendationController::class, 'exportPdf'])->name('recommendations.pdf');

    Route::get('/statistics', [StatisticController::class, 'index'])->name('statistics.index');
    Route::post('/statistics', [StatisticController::class, 'store'])->name('statistics.store');

    // Jalur untuk ekspor (Nanti akan digunakan Admin)
    Route::get('/statistics/{id}/export-excel', [StatisticController::class, 'exportExcel'])->name('statistics.export-excel');
    Route::get('/statistics/{id}/export-pdf', [StatisticController::class, 'exportPdf'])->name('statistics.export-pdf');
    Route::post('/statistics/send-satu-data/{id}', [StatisticController::class, 'sendToSatuData'])->name('statistics.send-satu-data');
    Route::post('/statistics/send-semua-data', [StatisticController::class, 'sendAllToSatuData'])->name('statistics.send-semua-data');
    Route::delete('/statistics/{id}', [StatisticController::class, 'destroyData'])->name('statistics.destroy-data');
    // Pastikan namanya persis 'statistics.finalize'
    Route::post('/statistics/finalize/{id}', [StatisticController::class, 'finalize'])->name('statistics.finalize');


    // Menampilkan halaman daftar sumber data
    Route::get('/sources', [DataSourceController::class, 'index'])->name('sources.index');
    // Menyimpan sumber data yang baru diinput
    Route::post('/sources/store', [DataSourceController::class, 'store'])->name('sources.store');
    Route::delete('/sources/{id}', [DataSourceController::class, 'destroy'])->name('sources.destroy');
    Route::put('/sources/{id}', [App\Http\Controllers\DataSourceController::class, 'update'])->name('sources.update');

    // Kategori
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Perangkat Daerah
    Route::get('/perangkat-daerah', [PerangkatDaerahController::class, 'index'])->name('perangkat-daerah.index');
    Route::post('/perangkat-daerah', [PerangkatDaerahController::class, 'store'])->name('perangkat-daerah.store');
    Route::delete('/perangkat-daerah/{id}', [PerangkatDaerahController::class, 'destroy'])->name('perangkat-daerah.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

});

require __DIR__ . '/auth.php';