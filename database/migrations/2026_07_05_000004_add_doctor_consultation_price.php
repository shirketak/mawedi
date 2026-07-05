<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->decimal('consultation_price', 12, 2)->default(0)->after('consultation_duration_minutes');
        });

        Schema::create('doctor_price_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->decimal('old_price', 12, 2);
            $table->decimal('new_price', 12, 2);
            $table->nullableMorphs('changed_by');
            $table->timestamps();

            $table->index(['doctor_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_price_logs');
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn('consultation_price');
        });
    }
};
