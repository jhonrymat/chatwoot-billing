<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Configuración del webhook
            $table->string('name'); // "Notificar en WhatsApp"
            $table->string('url'); // https://n8n.io/webhook/abc123
            $table->enum('event', [
                'account.created',
                'account.suspended',
                'account.activated',
                'payment.approved',
                'payment.failed',
                'subscription.activated',
                'subscription.cancelled',
                'subscription.renewed',
                'plan.limit_exceeded',
            ]);

            // Seguridad
            $table->string('secret')->nullable(); // Para firmar requests
            $table->boolean('is_active')->default(true);

            // Headers personalizados (JSON)
            $table->json('headers')->nullable(); // {"Authorization": "Bearer xxx"}

            // Configuración de reintentos
            $table->integer('max_retries')->default(3);
            $table->integer('timeout')->default(10); // segundos

            // Estadísticas
            $table->integer('success_count')->default(0);
            $table->integer('failure_count')->default(0);
            $table->timestamp('last_triggered_at')->nullable();

            $table->timestamps();

            // Índices
            $table->index(['user_id', 'event', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_webhooks');
    }
};
