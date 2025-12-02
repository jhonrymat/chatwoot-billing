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
        Schema::create('chatwoot_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Datos de Chatwoot
            $table->integer('chatwoot_account_id')->unique();
            $table->string('chatwoot_account_name');
            $table->integer('chatwoot_user_id')->nullable()->comment('ID del usuario administrador creado');

            // URLs de acceso
            $table->string('chatwoot_url')->nullable();
            $table->string('chatwoot_dashboard_url')->nullable();

            // Estado
            $table->enum('status', ['active', 'suspended', 'deleted'])->default('active');

            // Configuración inicial
            $table->string('locale', 10)->default('es');
            $table->string('timezone', 50)->default('America/Bogota');

            // Metadata de sincronización
            $table->timestamp('last_synced_at')->nullable();
            $table->text('sync_error')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('subscription_id');
            $table->index('user_id');
            $table->index('chatwoot_account_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatwoot_accounts');
    }
};
