<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\DayOfWeek;
use App\Enums\LibyaGovernorate;
use App\Enums\PaymentStatus;
use App\Enums\SlotStatus;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\DoctorSlot;
use App\Models\DoctorWorkingDay;
use App\Models\DoctorWorkingPeriod;
use App\Models\Hospital;
use App\Models\HospitalUser;
use App\Models\Patient;
use App\Models\Specialty;
use App\Services\DoctorSlotService;
use App\Services\HospitalSubscriptionService;
use App\Services\HospitalWalletService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $hospital = Hospital::query()->firstOrCreate(
            ['email' => 'hospital_test@mawedi.com'],
            [
                'name' => 'مستشفى طرابلس التعليمي',
                'governorate' => LibyaGovernorate::Tripoli,
                'phone' => '0910000000',
                'address' => 'طرابلس - ليبيا',
                'map_url' => 'https://maps.google.com',
                'is_active' => true,
            ]
        );

        if (! $hospital->wallet) {
            app(HospitalSubscriptionService::class)->initializeForNewHospital($hospital);
            app(HospitalWalletService::class)->deposit($hospital, 500, 'رصيد افتتاحي تجريبي');
        }

        HospitalUser::query()->firstOrCreate(
            ['email' => 'hospital_test@mawedi.com'],
            [
                'hospital_id' => $hospital->id,
                'name' => 'مستشفى طرابلس التعليمي',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $specialty = Specialty::query()->where('name', 'طب عام')->first();
        if ($specialty) {
            $hospital->specialties()->syncWithoutDetaching([$specialty->id]);
        }

        $doctor = Doctor::query()->firstOrCreate(
            ['hospital_id' => $hospital->id, 'name' => 'د. أحمد محمد'],
            [
                'specialty_id' => $specialty?->id ?? Specialty::first()->id,
                'consultation_duration_minutes' => 20,
                'consultation_price' => 50,
                'is_active' => true,
            ]
        );

        if ($doctor->workingDays()->count() === 0) {
            $sunday = DoctorWorkingDay::create([
                'doctor_id' => $doctor->id,
                'day_of_week' => DayOfWeek::Sunday,
            ]);
            DoctorWorkingPeriod::create([
                'doctor_working_day_id' => $sunday->id,
                'start_time' => '09:00',
                'end_time' => '13:00',
            ]);
            DoctorWorkingPeriod::create([
                'doctor_working_day_id' => $sunday->id,
                'start_time' => '17:00',
                'end_time' => '21:00',
            ]);

            app(DoctorSlotService::class)->regenerateSlotsForDoctor($doctor);
        }

        $patient = Patient::query()->where('phone', '0911111111')->first();

        $slot = DoctorSlot::query()
            ->where('doctor_id', $doctor->id)
            ->where('status', SlotStatus::Available)
            ->orderBy('date')
            ->orderBy('start_time')
            ->first();

        if ($slot && ! Booking::query()->where('doctor_slot_id', $slot->id)->exists()) {
            Booking::create([
                'hospital_id' => $hospital->id,
                'doctor_id' => $doctor->id,
                'specialty_id' => $doctor->specialty_id,
                'doctor_slot_id' => $slot->id,
                'patient_id' => $patient?->id,
                'patient_name' => $patient?->name ?? 'محمد علي',
                'patient_phone' => $patient?->phone ?? '0911111111',
                'booking_date' => $slot->date,
                'booking_time' => $slot->start_time,
                'status' => BookingStatus::Confirmed,
                'payment_status' => PaymentStatus::Unpaid,
                'consultation_price' => $doctor->consultation_price,
            ]);
            $slot->update(['status' => SlotStatus::Booked]);
        }
    }
}
