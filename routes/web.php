<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HospitalController as AdminHospitalController;
use App\Http\Controllers\Admin\SpecialtyController as AdminSpecialtyController;
use App\Http\Controllers\Hospital\AuthController as HospitalAuthController;
use App\Http\Controllers\Hospital\BookingController;
use App\Http\Controllers\Hospital\DashboardController as HospitalDashboardController;
use App\Http\Controllers\Hospital\DoctorController;
use App\Http\Controllers\Hospital\ProfileController;
use App\Http\Controllers\Hospital\RescheduleLogController;
use App\Http\Controllers\Hospital\SpecialtyController as HospitalSpecialtyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('hospital.login');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login']);
    });

    Route::middleware('admin')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::patch('hospitals/{hospital}/toggle-status', [AdminHospitalController::class, 'toggleStatus'])
            ->name('hospitals.toggle-status');
        Route::resource('hospitals', AdminHospitalController::class)->except(['show']);

        Route::patch('specialties/{specialty}/toggle-status', [AdminSpecialtyController::class, 'toggleStatus'])
            ->name('specialties.toggle-status');
        Route::resource('specialties', AdminSpecialtyController::class)->except(['show']);
    });
});

// Hospital Routes
Route::prefix('hospital')->name('hospital.')->group(function () {
    Route::middleware('guest:hospital')->group(function () {
        Route::get('login', [HospitalAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [HospitalAuthController::class, 'login']);
    });

    Route::middleware('hospital.auth')->group(function () {
        Route::post('logout', [HospitalAuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [HospitalDashboardController::class, 'index'])->name('dashboard');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('specialties', [HospitalSpecialtyController::class, 'index'])->name('specialties.index');
        Route::post('specialties', [HospitalSpecialtyController::class, 'store'])->name('specialties.store');
        Route::delete('specialties/{specialty}', [HospitalSpecialtyController::class, 'destroy'])->name('specialties.destroy');

        Route::patch('doctors/{doctor}/toggle-status', [DoctorController::class, 'toggleStatus'])
            ->name('doctors.toggle-status');
        Route::get('doctors/{doctor}/schedule', [DoctorController::class, 'schedule'])->name('doctors.schedule');
        Route::put('doctors/{doctor}/schedule', [DoctorController::class, 'updateSchedule'])->name('doctors.schedule.update');
        Route::get('doctors/{doctor}/vacations', [DoctorController::class, 'vacations'])->name('doctors.vacations');
        Route::post('doctors/{doctor}/vacations', [DoctorController::class, 'storeVacation'])->name('doctors.vacations.store');
        Route::delete('doctors/{doctor}/vacations/{vacation}', [DoctorController::class, 'destroyVacation'])->name('doctors.vacations.destroy');
        Route::post('doctors/{doctor}/postpone', [DoctorController::class, 'postpone'])->name('doctors.postpone');
        Route::resource('doctors', DoctorController::class)->except(['show']);

        Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('reschedule-logs', [RescheduleLogController::class, 'index'])->name('reschedule-logs.index');
    });
});
