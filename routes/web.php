<?php

use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionType;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DoctorPriceController;
use App\Http\Controllers\Admin\HospitalController as AdminHospitalController;
use App\Http\Controllers\Admin\HospitalStatsController;
use App\Http\Controllers\Admin\HospitalSubscriptionController;
use App\Http\Controllers\Admin\HospitalWalletController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SpecialtyController as AdminSpecialtyController;
use App\Http\Controllers\Hospital\AccountController;
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

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('system-overview', [ReportController::class, 'systemOverview'])->name('system-overview');
            Route::get('hospitals-stats', [ReportController::class, 'hospitalsStats'])->name('hospitals-stats');
            Route::get('bookings-stats', [ReportController::class, 'bookingsStats'])->name('bookings-stats');
            Route::get('financial-stats', [ReportController::class, 'financialStats'])->name('financial-stats');
            Route::get('daily-bookings', [ReportController::class, 'dailyBookings'])->name('daily-bookings');
            Route::get('monthly-bookings', [ReportController::class, 'monthlyBookings'])->name('monthly-bookings');
            Route::get('user-growth', [ReportController::class, 'userGrowth'])->name('user-growth');
            Route::get('hospital-growth', [ReportController::class, 'hospitalGrowth'])->name('hospital-growth');
            Route::get('top-hospitals', [ReportController::class, 'topHospitals'])->name('top-hospitals');
            Route::get('top-specialties', [ReportController::class, 'topSpecialties'])->name('top-specialties');
        });

        Route::patch('hospitals/{hospital}/toggle-status', [AdminHospitalController::class, 'toggleStatus'])
            ->name('hospitals.toggle-status');
        Route::get('hospitals/{hospital}/stats', [HospitalStatsController::class, 'show'])
            ->name('hospitals.show');
        Route::get('hospitals/{hospital}/wallet', [HospitalWalletController::class, 'index'])
            ->name('hospitals.wallet');
        Route::post('hospitals/{hospital}/wallet', [HospitalWalletController::class, 'store'])
            ->name('hospitals.wallet.store');
        Route::put('hospitals/{hospital}/subscription', [HospitalSubscriptionController::class, 'update'])
            ->name('hospitals.subscription.update');
        Route::post('hospitals/{hospital}/free-trial', [HospitalSubscriptionController::class, 'grantTrial'])
            ->name('hospitals.free-trial');
        Route::patch('hospitals/{hospital}/activate', [HospitalSubscriptionController::class, 'activate'])
            ->name('hospitals.activate');
        Route::resource('hospitals', AdminHospitalController::class)->except(['show']);

        Route::get('doctors/{doctor}/price', [DoctorPriceController::class, 'edit'])->name('doctors.price.edit');
        Route::put('doctors/{doctor}/price', [DoctorPriceController::class, 'update'])->name('doctors.price.update');

        Route::patch('patients/{patient}/toggle-status', [PatientController::class, 'toggleStatus'])
            ->name('patients.toggle-status');
        Route::resource('patients', PatientController::class)->only(['index', 'show', 'edit', 'update']);

        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

        Route::patch('admin-users/{admin_user}/toggle-status', [AdminUserController::class, 'toggleStatus'])
            ->name('admin-users.toggle-status');
        Route::resource('admin-users', AdminUserController::class)->except(['show']);

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

        Route::get('account', [AccountController::class, 'index'])->name('account.index');

        Route::get('bookings/create', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::patch('bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
        Route::patch('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::patch('bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
        Route::patch('bookings/{booking}/no-show', [BookingController::class, 'noShow'])->name('bookings.no-show');
        Route::patch('bookings/{booking}/mark-paid', [BookingController::class, 'markPaid'])->name('bookings.mark-paid');
        Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');

        Route::get('reschedule-logs', [RescheduleLogController::class, 'index'])->name('reschedule-logs.index');
    });
});
