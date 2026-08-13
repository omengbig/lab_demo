<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\AttendanceImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [SemesterController::class, 'index'])->name('dashboard');

    Route::post('/semesters', [SemesterController::class, 'store'])->name('semesters.store');
    Route::get('/semesters/{semester}', [SemesterController::class, 'show'])->name('semesters.show');

    Route::post('/semesters/{semester}/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');

    Route::get('/groups/{group}/import', [AttendanceImportController::class, 'create'])->name('groups.import');
    Route::post('/groups/{group}/import', [AttendanceImportController::class, 'store'])->name('groups.import.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
