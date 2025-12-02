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
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();

            // Datos del webhook
            $table->string('event_type', 100);
            $table->string('resource_type', 50)->nullable();
            $table->string('resource_id')->nullable();

            // Payload
            $table->json('payload');

            // Procesamiento
            $table->enum('status', ['pending', 'processing', 'processed', 'failed'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('attempts')->default(0);

            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Índices
            $table->index('event_type');
            $table->index('status');
            $table->index('resource_id');
            $table->index('created_at');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
