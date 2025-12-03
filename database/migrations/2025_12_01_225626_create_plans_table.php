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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('COP');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');

            // Límites del plan
            $table->integer('max_agents')->default(5);
            $table->integer('max_inboxes')->default(3);
            $table->integer('max_contacts')->default(1000);
            $table->integer('max_conversations_per_month')->nullable();

            // Features adicionales
            $table->json('features')->nullable();

            // Control
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->integer('sort_order')->default(0);

            // MercadoPago
            $table->string('gateway_plan_id')->nullable();

            $table->timestamps();

            // Índices
            $table->index('slug');
            $table->index('is_active');
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
