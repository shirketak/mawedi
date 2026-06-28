<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('map_url')->nullable();
            $table->string('governorate');
            $table->string('phone');
            $table->string('phone_secondary')->nullable();
            $table->string('email');
            $table->string('website')->nullable();
            $table->text('address');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('governorate');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
