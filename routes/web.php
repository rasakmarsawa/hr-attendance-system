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
    //user routes
    Route::resource('user', App\Http\Controllers\UserController::class)->except('edit','update');

    //department routes
    Route::resource('department', App\Http\Controllers\DepartmentController::class)->except('show');

    //employee routes
    Route::resource('employee', App\Http\Controllers\EmployeeController::class)->except('create')->except('index', 'show', 'create');
    Route::get('/employee/create/{user}', [App\Http\Controllers\EmployeeController::class, 'create'])->name('employee.create');

    //attendance routes
    Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');   
    Route::post('/attendance/pre-fill', [App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/report', [App\Http\Controllers\AttendanceController::class, 'report'])->name('attendance.report');
    Route::get('/attendance/export', App\Http\Controllers\AttendanceExportController::class)->name('attendance.export');
    Route::get('/attendance/detail/{user_id}/{month}/{year}', [App\Http\Controllers\AttendanceController::class, 'detail'])->name('attendance.detail');

    //payroll routes
    Route::resource('payroll', App\Http\Controllers\PayrollController::class)->only(['edit', 'update']);
    Route::get('/payroll/index/{month}/{year}', [App\Http\Controllers\PayrollController::class, 'index'])->name('payroll.index');
    
    //bulk routes
    Route::post('/payroll/finalize-all/{month}/{year}', [App\Http\Controllers\PayrollController::class, 'finalizeAll'])->name('payroll.finalizeAll');
    Route::post('/payroll/generate/{month}/{year}', [App\Http\Controllers\PayrollController::class, 'generate'])->name('payroll.generate');
    Route::get('/payroll/export/{month}/{year}', [App\Http\Controllers\PayrollController::class, 'export'])->name('payroll.export');

    //single routes
    Route::post('/payroll/{payroll}/finalize', [App\Http\Controllers\PayrollController::class, 'finalize'])->name('payroll.finalize');          

    Route::put('/payroll/pay/{payroll}', [App\Http\Controllers\PayrollController::class, 'pay'])->name('payroll.pay');
});

Route::middleware('auth')->group(function () {
    Route::get('/my-profile/{user}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');
    Route::put('/my-profile/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('user.update');        
    
    Route::post('/check-in', [App\Http\Controllers\AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/check-out', [App\Http\Controllers\AttendanceController::class, 'checkOut'])->name('attendance.checkout');

    Route::get('/my-payroll', [App\Http\Controllers\PayrollController::class, 'myPayroll'])->name('payroll.myPayroll'); 
    Route::get('/payroll/exportOne/{payroll}', [App\Http\Controllers\PayrollController::class, 'exportOne'])->name('payroll.exportOne');
});

require __DIR__.'/auth.php';
