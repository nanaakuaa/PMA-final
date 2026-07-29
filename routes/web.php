<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;


// Public entry: send guests to login, authenticated users to dashboard
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

// Guest auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Password Manager
    Route::get('/passwords', function () {
        return inertia('PasswordManager');
    })->name('passwords.index');

    // Folder Manager
    Route::get('/folders', function () {
        return inertia('FolderManager');
    })->name('folders.index');

    // Audit Log (Admin Only)
    Route::get('/audit', function () {
        return inertia('Audit');
    })->name('audit')->middleware('admin');

    // Password Generator
    Route::get('/generator', function () {
        return inertia('Generator');
    })->name('generator');

    // Settings
    Route::get('/settings', function () {
        return inertia('Settings', [
            'user' => auth()->user()
        ]);
    })->name('settings');

    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/departments', [\App\Http\Controllers\Admin\DepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments', [\App\Http\Controllers\Admin\DepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{department}', [\App\Http\Controllers\Admin\DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [\App\Http\Controllers\Admin\DepartmentController::class, 'destroy'])->name('departments.destroy');
    });
});


