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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->string('payment_gateway', 50)->default('mercadopago');

            // Estado de la suscripción
            $table->enum('status', ['pending', 'active', 'cancelled', 'expired', 'suspended'])->default('pending');

            // Fechas importantes
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();

            // MercadoPago
            $table->string('gateway_subscription_id')->unique()->nullable();
            $table->string('gateway_customer_id')->nullable();
            $table->string('mgateway_preapproval_id')->nullable();

            // Facturación
            $table->date('next_billing_date')->nullable();
            $table->date('last_payment_date')->nullable();

            $table->timestamps();

            // Índices
            $table->index('user_id');
            $table->index('plan_id');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index('gateway_subscription_id');
            $table->index('next_billing_date');
            $table->index('payment_gateway');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
