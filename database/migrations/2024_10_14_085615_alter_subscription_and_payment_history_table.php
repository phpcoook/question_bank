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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('customer_response');
            $table->dropColumn('payment_response');
            $table->text('stripe_subscription_id')->nullable(true);
        });
        Schema::table('payment_history', function (Blueprint $table) {
            $table->dropColumn('customer_response');
            $table->dropColumn('payment_response');
            $table->text('stripe_subscription_id')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->longText('payment_response')->nullable(true);
            $table->longText('customer_response')->nullable(true);
            $table->dropColumn('stripe_subscription_id');
        });
        Schema::table('payment_history', function (Blueprint $table) {
            $table->longText('payment_response')->nullable(true);
            $table->longText('customer_response')->nullable(true);
            $table->dropColumn('stripe_subscription_id');
        });
    }
};
