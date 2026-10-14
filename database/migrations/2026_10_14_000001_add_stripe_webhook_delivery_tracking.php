<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stripe delivers webhooks at least once and in no guaranteed order:
     * - stripe_webhook_events records each processed event id, so a redelivery is a no-op;
     * - subscriptions.stripe_event_at is the creation time of the last event applied,
     *   so an older event that arrives late cannot overwrite newer state.
     */
    public function up(): void
    {
        Schema::create('stripe_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_event_id')->unique();
            $table->string('type');
            $table->timestamp('processed_at');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->timestamp('stripe_event_at')->nullable()->after('canceled_at');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('stripe_event_at');
        });

        Schema::dropIfExists('stripe_webhook_events');
    }
};
