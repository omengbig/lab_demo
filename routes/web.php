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
    Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');

    Route::get('/groups/{group}/import', [AttendanceImportController::class, 'create'])->name('groups.import');
    Route::post('/groups/{group}/import', [AttendanceImportController::class, 'store'])->name('groups.import.store');
    
    Route::get('/groups/{group}/export/excel', [\App\Http\Controllers\GroupExportController::class, 'excel'])->name('groups.export.excel');
    Route::get('/groups/{group}/export/pdf', [\App\Http\Controllers\GroupExportController::class, 'pdf'])->name('groups.export.pdf');

    Route::get('/groups/{group}/roster', [\App\Http\Controllers\RosterImportController::class, 'create'])->name('groups.roster');
    Route::post('/groups/{group}/roster', [\App\Http\Controllers\RosterImportController::class, 'store'])->name('groups.roster.store');

    Route::post('/attendances/{student}/{session}/reason', [\App\Http\Controllers\AttendanceController::class, 'updateReason'])->name('attendances.reason');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
