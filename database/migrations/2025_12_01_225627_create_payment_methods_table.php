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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('payment_gateway', 50)->default('mercadopago');

            // Información del método de pago
            $table->enum('type', ['credit_card', 'debit_card', 'pse', 'efecty', 'other']);
            $table->boolean('is_default')->default(false);

            // gateway Card Token
            $table->string('gateway_card_id')->nullable();
            $table->string('gateway_customer_id')->nullable();

            // Información enmascarada (para mostrar al usuario)
            $table->string('last_four_digits', 4)->nullable();
            $table->string('card_brand', 50)->nullable()->comment('visa, mastercard, amex, etc');
            $table->string('expiration_month', 2)->nullable();
            $table->string('expiration_year', 4)->nullable();
            $table->string('cardholder_name')->nullable();

            // Estado
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Índices
            $table->index('user_id');
            $table->index(['user_id', 'is_default']);
            $table->index('gateway_customer_id');
            $table->index('payment_gateway');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
