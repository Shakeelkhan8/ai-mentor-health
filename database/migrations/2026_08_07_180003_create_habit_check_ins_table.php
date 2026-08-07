<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habit_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('check_in_date');
            $table->string('status', 16);
            $table->text('note')->nullable();
            $table->unsignedTinyInteger('mood_score')->nullable();
            $table->timestamps();

            $table->unique(['habit_id', 'check_in_date']);
            $table->index(['user_id', 'check_in_date']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habit_check_ins');
    }
};
