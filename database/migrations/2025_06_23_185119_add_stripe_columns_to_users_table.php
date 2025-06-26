<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_subscribed')->default(false); // Ensure the id column is set as primary key
            $table->string('stripe_payment_intent')->nullable()->comment('Stripe payement intent/method');
            $table->foreignId('plan_id')->default(1)->comment('Stripe ID of the plan')->constrained();
            $table->string('subscription_plan')->nullable()->comment('Current subscription plan slug');
            $table->string('stripe_price')->nullable()->comment('Stripe price id for the plan');
            $table->string('stripe_customer_id')->nullable()->comment('Stripe customer ID');
            $table->string('stripe_subscription_id')->nullable()->comment('Stripe subscription ID');
            $table->string('subscription_status')->nullable()->comment('subscription status'); // active, canceled, trialing
            $table->string('stripe_payment_status')->nullable()->comment('Payment status (e.g., paid, incopmplete, failed)');
            $table->string('stripe_payment_type')->nullable()->comment('Payment method used for the subscription (e.g., card, wallet)');
            $table->timestamp('subscription_started_at')->nullable()->comment('Subscription start date');
            $table->timestamp('subscription_ends_at')->nullable()->comment('Subscription end date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
