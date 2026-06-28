<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Specialty;
use App\Policies\BookingPolicy;
use App\Policies\DoctorPolicy;
use App\Policies\HospitalPolicy;
use App\Policies\SpecialtyPolicy;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\BookingRescheduleLogRepositoryInterface;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use App\Repositories\Contracts\HospitalRepositoryInterface;
use App\Repositories\Contracts\SpecialtyRepositoryInterface;
use App\Repositories\Eloquent\BookingRepository;
use App\Repositories\Eloquent\BookingRescheduleLogRepository;
use App\Repositories\Eloquent\DoctorRepository;
use App\Repositories\Eloquent\HospitalRepository;
use App\Repositories\Eloquent\SpecialtyRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(HospitalRepositoryInterface::class, HospitalRepository::class);
        $this->app->bind(SpecialtyRepositoryInterface::class, SpecialtyRepository::class);
        $this->app->bind(DoctorRepositoryInterface::class, DoctorRepository::class);
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->bind(BookingRescheduleLogRepositoryInterface::class, BookingRescheduleLogRepository::class);
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();

        Gate::policy(Hospital::class, HospitalPolicy::class);
        Gate::policy(Specialty::class, SpecialtyPolicy::class);
        Gate::policy(Doctor::class, DoctorPolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);
    }
}
