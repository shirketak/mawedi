<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->string('subscription_type')->default('monthly')->after('is_active');
            $table->string('subscription_status')->default('trial')->after('subscription_type');
            $table->decimal('monthly_price', 12, 2)->nullable()->after('subscription_status');
            $table->decimal('usage_fee_per_booking', 12, 2)->nullable()->after('monthly_price');
            $table->date('subscription_starts_at')->nullable()->after('usage_fee_per_booking');
            $table->date('subscription_ends_at')->nullable()->after('subscription_starts_at');
            $table->unsignedInteger('free_trial_days')->default(0)->after('subscription_ends_at');
            $table->date('trial_ends_at')->nullable()->after('free_trial_days');
            $table->string('deactivation_reason')->nullable()->after('trial_ends_at');
            $table->timestamp('deactivated_at')->nullable()->after('deactivation_reason');
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_type',
                'subscription_status',
                'monthly_price',
                'usage_fee_per_booking',
                'subscription_starts_at',
                'subscription_ends_at',
                'free_trial_days',
                'trial_ends_at',
                'deactivation_reason',
                'deactivated_at',
            ]);
        });
    }
};
