<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
// Public welcome page
Route::get('/', function () {
    return redirect()->route('login');
});



// Request reset link
Route::get('password/reset', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')->name('password.request');

Route::post('password/email', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')->name('password.email');

// Show reset form
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')->name('password.reset');

Route::post('password/reset', [NewPasswordController::class, 'store'])
    ->middleware('guest')->name('password.update');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('role:Admin')->get('/admin/dashboard', function () {
        return view('dashboard', ['role' => 'Admin']);
    })->name('admin.dashboard');

    Route::middleware('role:CRM Agent')->get('/crm/dashboard', function () {
        return view('dashboard', ['role' => 'CRM Agent']);
    })->name('crm.dashboard');

    Route::middleware('role:Doctor')->get('/doctor/dashboard', function () {
        return view('dashboard', ['role' => 'Doctor']);
    })->name('doctor.dashboard');

    Route::middleware('role:Patient')->get('/patient/dashboard', function () {
        return view('dashboard', ['role' => 'Patient']);
    })->name('patient.dashboard');

    Route::middleware('role:Lab Manager')->get('/lab/dashboard', function () {
        return view('dashboard', ['role' => 'Lab Manager']);
    })->name('lab.dashboard');
});

// Authenticated user profile routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/password/change', [ProfileController::class, 'editPassword'])->name('password.change');
    Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});


// ============================
// Role-Based Dashboards
// ============================
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin/users', [UserRoleController::class, 'index']);
    Route::post('/admin/users/{id}/assign-roles', [UserRoleController::class, 'assign']);
});



require __DIR__.'/auth.php';
