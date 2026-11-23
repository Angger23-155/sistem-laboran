<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ComplainController;
use App\Http\Controllers\MasterTugasHarianController;
use App\Http\Controllers\HariLiburController;
use App\Http\Controllers\ReportController;

// Rute agar saat buka localhost:8000 langsung diarahkan ke halaman login
Route::get('/', function () {
    return redirect('/login');
});

// Rute untuk Tamu (Guest) - Belum Login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Rute untuk User yang Sudah Login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rute dashboard menggunakan controller
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- RUTE MANAJEMEN TUGAS ---
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('tasks.show');
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Rute untuk melaporkan penyelesaian tugas
    Route::post('/tasks/{taskId}/report', [TaskReportController::class, 'store'])->name('tasks.report');
    // Rute untuk memproses persetujuan / revisi dari Kepala Lab
    Route::post('/tasks/{id}/approve', [TaskController::class, 'approve'])->name('tasks.approve');

    Route::post('/tasks/bulk-delete', [TaskController::class, 'bulkDelete'])->name('tasks.bulkDelete');
    // Rute untuk menampilkan halaman edit
    Route::get('/tasks/{id}/edit', [App\Http\Controllers\TaskController::class, 'edit'])->name('tasks.edit');

    // Rute untuk memproses penyimpanan hasil edit
    Route::put('/tasks/{id}', [App\Http\Controllers\TaskController::class, 'update'])->name('tasks.update');

    // --- RUTE KHUSUS SUPERADMIN (CRUD MANAJEMEN PENGGUNA & MASTER TUGAS) ---
    Route::middleware([\App\Http\Middleware\CheckSuperAdmin::class])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        // Pindahkan master-tugas dan hari-libur ke sini agar HANYA SuperAdmin yang bisa akses
        Route::resource('master-tugas', MasterTugasHarianController::class);
        Route::resource('hari-libur', HariLiburController::class);
    });
    // Route Laporan Kinerja
    Route::get('/laporan-tugas', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/laporan-tugas/cetak', [ReportController::class, 'cetakPdf'])->name('reports.cetak');

    // --- RUTE SISTEM KOMPLAIN ---
    Route::get('/complains', [ComplainController::class, 'index'])->name('complains.index');
    Route::post('/complains', [ComplainController::class, 'store'])->name('complains.store');
    Route::post('/complains/{id}/respond', [ComplainController::class, 'respond'])->name('complains.respond');
    Route::delete('/complains/{id}', [ComplainController::class, 'destroy'])->name('complains.destroy');
    Route::post('/complains/{id}/complete', [ComplainController::class, 'complete'])->name('complains.complete');
});