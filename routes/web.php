<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth','role:Admin'])->group(function () {
    Route::resource('user', App\Http\Controllers\UserController::class)->except('edit','update');
    Route::resource('department', App\Http\Controllers\DepartmentController::class);
    Route::resource('employee', App\Http\Controllers\EmployeeController::class)->except('create');
    Route::get('/employee/create/{user}', [App\Http\Controllers\EmployeeController::class, 'create'])->name('employee.create');
});

Route::middleware('auth')->group(function () {
    Route::get('/user/{user}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('user.update');        
});

require __DIR__.'/auth.php';
