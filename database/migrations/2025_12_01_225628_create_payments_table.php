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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('payment_gateway', 50)->default('mercadopago');
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();

            // Información del pago
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('COP');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'refunded', 'charged_back']);

            // Pago
            $table->string('gateway_payment_id')->unique();
            $table->string('gateway_status', 50)->nullable();
            $table->string('gateway_status_detail')->nullable();
            $table->string('payment_method_id_gateway', 50)->nullable()->comment('ID del método en MercadoPago');
            $table->string('payment_type', 50)->nullable();

            // Metadata
            $table->json('metadata')->nullable()->comment('Datos adicionales del pago de MercadoPago');

            // Fechas
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Índices
            $table->index('subscription_id');
            $table->index('user_id');
            $table->index('payment_method_id');
            $table->index('status');
            $table->index('gateway_payment_id');
            $table->index(['user_id', 'status']);
            $table->index('paid_at');
            $table->index('payment_gateway');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
