<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('focus_area', 32);
            $table->string('frequency', 16)->default('daily');
            $table->unsignedTinyInteger('target_per_period')->default(1);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['user_id', 'focus_area']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
