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
        Schema::create('chatwoot_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chatwoot_account_id')->constrained()->cascadeOnDelete();

            // Métricas generales
            $table->integer('total_conversations')->default(0);
            $table->integer('open_conversations')->default(0);
            $table->integer('resolved_conversations')->default(0);
            $table->integer('pending_conversations')->default(0);

            // Agentes e Inboxes
            $table->integer('total_agents')->default(0);
            $table->integer('active_agents')->default(0);
            $table->integer('total_inboxes')->default(0);
            $table->integer('total_contacts')->default(0);

            // Estadísticas de tiempo
            $table->integer('avg_first_response_time')->nullable()->comment('En segundos');
            $table->integer('avg_resolution_time')->nullable()->comment('En segundos');

            // Periodo de las métricas
            $table->date('metrics_date');

            // Datos raw de la API
            $table->json('raw_data')->nullable();

            $table->timestamps();

            // Índices
            $table->index('chatwoot_account_id');
            $table->index('metrics_date');
            $table->unique(['chatwoot_account_id', 'metrics_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatwoot_metrics');
    }
};
