<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('focus_areas')->nullable();
            $table->text('primary_goal')->nullable();
            $table->string('timezone', 64)->default('UTC');
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->time('daily_reminder_time')->nullable();
            $table->json('preferences')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
