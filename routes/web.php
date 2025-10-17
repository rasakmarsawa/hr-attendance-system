<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth','role:Admin'])->group(function () {
    Route::resource('user', App\Http\Controllers\UserController::class)->except('edit','update');

    Route::resource('department', App\Http\Controllers\DepartmentController::class);

    Route::resource('employee', App\Http\Controllers\EmployeeController::class)->except('create');
    Route::get('/employee/create/{user}', [App\Http\Controllers\EmployeeController::class, 'create'])->name('employee.create');

    Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');   
    Route::post('/attendance/pre-fill', [App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/report', [App\Http\Controllers\AttendanceController::class, 'report'])->name('attendance.report');
    Route::get('/attendance/export', App\Http\Controllers\AttendanceExportController::class)->name('attendance.export');

    Route::resource('payroll', App\Http\Controllers\PayrollController::class);
    Route::get('/payroll/monthly', [App\Http\Controllers\PayrollController::class, 'monthly'])->name('payroll.monthly');
    Route::post('/payroll/generate', [App\Http\Controllers\PayrollController::class, 'generate'])->name('payroll.generate');
    Route::post('/payroll/{payroll}/finalize', [App\Http\Controllers\PayrollController::class, 'finalize'])->name('payroll.finalize');  
    route::post('/payroll/finalize-all', [App\Http\Controllers\PayrollController::class, 'finalizeAll'])->name('payroll.finalizeAll');  
});

Route::middleware('auth')->group(function () {
    Route::get('/user/{user}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('user.update');        
    
    Route::post('/check-in', [App\Http\Controllers\AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/check-out', [App\Http\Controllers\AttendanceController::class, 'checkOut'])->name('attendance.checkout');
});

require __DIR__.'/auth.php';
