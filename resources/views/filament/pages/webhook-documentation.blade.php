<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Introducción --}}
        <x-filament::section :heading="'🔗 Webhooks - Notificaciones en Tiempo Real'"
            description="Recibe notificaciones instantáneas cuando ocurran eventos importantes en tu cuenta. Integra fácilmente con n8n, Zapier, Make.com o tu propio sistema.">
        </x-filament::section>

        {{-- Eventos Disponibles --}}
        <x-filament::section>
            <x-slot name="heading">
                🚀 Eventos Disponibles
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-filament::card>
                    <div class="font-semibold text-success-600 dark:text-success-400 mb-2">📊 Cuenta</div>
                    <ul class="space-y-1 text-sm">
                        <li><code class="text-xs">account.created</code> - Cuenta creada</li>
                        <li><code class="text-xs">account.suspended</code> - Cuenta suspendida</li>
                        <li><code class="text-xs">account.activated</code> - Cuenta reactivada</li>
                    </ul>
                </x-filament::card>

                <x-filament::card>
                    <div class="font-semibold text-primary-600 dark:text-primary-400 mb-2">💳 Pagos</div>
                    <ul class="space-y-1 text-sm">
                        <li><code class="text-xs">payment.approved</code> - Pago aprobado</li>
                        <li><code class="text-xs">payment.failed</code> - Pago fallido</li>
                    </ul>
                </x-filament::card>

                <x-filament::card>
                    <div class="font-semibold text-warning-600 dark:text-warning-400 mb-2">📅 Suscripciones</div>
                    <ul class="space-y-1 text-sm">
                        <li><code class="text-xs">subscription.activated</code> - Activada</li>
                        <li><code class="text-xs">subscription.cancelled</code> - Cancelada</li>
                        <li><code class="text-xs">subscription.renewed</code> - Renovada</li>
                    </ul>
                </x-filament::card>

                <x-filament::card>
                    <div class="font-semibold text-danger-600 dark:text-danger-400 mb-2">⚠️ Límites</div>
                    <ul class="space-y-1 text-sm">
                        <li><code class="text-xs">plan.limit_exceeded</code> - Límite excedido</li>
                    </ul>
                </x-filament::card>
            </div>
        </x-filament::section>

        {{-- Formato del Payload --}}
        <x-filament::section>
            <x-slot name="heading">
                📦 Formato del Payload
            </x-slot>
            <x-slot name="description">
                Todos los webhooks envían una petición POST con el siguiente formato JSON:
            </x-slot>

            <div class="rounded-lg bg-gray-950 p-4 font-mono text-sm text-white overflow-x-auto">
                <pre><code>{
  "event": "account.created",
  "timestamp": "2025-01-15T10:30:00Z",
  "signature": "sha256_hash_if_secret_provided",
  "data": {
    "chatwoot_account_id": 123,
    "account_name": "Mi Empresa",
    "dashboard_url": "https://chatwoot.com/app/accounts/123/dashboard",
    "plan": {
      "name": "Plan Profesional",
      "price": 99900
    }
  }
}</code></pre>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div class="flex items-start gap-2">
                    <x-filament::badge color="gray">event</x-filament::badge>
                    <span class="text-gray-600 dark:text-gray-400">Tipo de evento disparado</span>
                </div>
                <div class="flex items-start gap-2">
                    <x-filament::badge color="gray">timestamp</x-filament::badge>
                    <span class="text-gray-600 dark:text-gray-400">Fecha/hora en formato ISO 8601</span>
                </div>
                <div class="flex items-start gap-2">
                    <x-filament::badge color="gray">signature</x-filament::badge>
                    <span class="text-gray-600 dark:text-gray-400">HMAC SHA-256 para verificación</span>
                </div>
                <div class="flex items-start gap-2">
                    <x-filament::badge color="gray">data</x-filament::badge>
                    <span class="text-gray-600 dark:text-gray-400">Datos específicos del evento</span>
                </div>
            </div>
        </x-filament::section>

        {{-- Verificar Firma --}}
        <x-filament::section>
            <x-slot name="heading">
                🔐 Verificar Firma (Recomendado)
            </x-slot>


            <x-alert type="warning" icon="document-duplicate">
                <strong>Importante:</strong> Siempre configura un secret y verifica la firma para asegurar
                que los webhooks provienen realmente de tu sistema.
            </x-alert>

            <div class="mt-4 space-y-4">
                {{-- Node.js --}}
                <div>
                    <h4 class="text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Node.js / n8n</h4>
                    <div class="rounded-lg bg-gray-950 p-4 font-mono text-xs text-white overflow-x-auto">
                        <pre><code>const crypto = require('crypto');

function verifyWebhook(payload, receivedSignature, secret) {
  const signature = crypto
    .createHmac('sha256', secret)
    .update(JSON.stringify(payload.data))
    .digest('hex');

  return signature === receivedSignature;
}

// Uso
if (verifyWebhook(payload, payload.signature, 'tu_secret')) {
  console.log('✅ Webhook válido');
} else {
  console.log('❌ Firma inválida');
}</code></pre>
                    </div>
                </div>

                {{-- PHP --}}
                <div>
                    <h4 class="text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">PHP</h4>
                    <div class="rounded-lg bg-gray-950 p-4 font-mono text-xs text-white overflow-x-auto">
                        <pre><code>function verifyWebhook($payload, $receivedSignature, $secret) {
    $signature = hash_hmac(
        'sha256',
        json_encode($payload['data']),
        $secret
    );

    return hash_equals($signature, $receivedSignature);
}

// Uso
$payload = json_decode(file_get_contents('php://input'), true);
if (verifyWebhook($payload, $payload['signature'], 'tu_secret')) {
    // ✅ Webhook válido
}</code></pre>
                    </div>
                </div>

                {{-- Python --}}
                <div>
                    <h4 class="text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Python</h4>
                    <div class="rounded-lg bg-gray-950 p-4 font-mono text-xs text-white overflow-x-auto">
                        <pre><code>import hmac
import hashlib
import json

def verify_webhook(payload, received_signature, secret):
    signature = hmac.new(
        secret.encode('utf-8'),
        json.dumps(payload['data']).encode('utf-8'),
        hashlib.sha256
    ).hexdigest()

    return hmac.compare_digest(signature, received_signature)</code></pre>
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Casos de Uso --}}
        <x-filament::section>
            <x-slot name="heading">
                💡 Casos de Uso e Integraciones
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-filament::card>
                    <div class="text-2xl mb-2">📱</div>
                    <div class="font-semibold mb-1">WhatsApp / SMS</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Notificar por WhatsApp usando n8n + Twilio o Evolution API
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-2xl mb-2">📊</div>
                    <div class="font-semibold mb-1">Dashboard Personalizado</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Actualizar métricas en tiempo real en tu panel de control
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-2xl mb-2">💬</div>
                    <div class="font-semibold mb-1">Notificaciones de Equipo</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Enviar alertas a Slack, Discord o Telegram
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-2xl mb-2">📧</div>
                    <div class="font-semibold mb-1">Email Marketing</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Sincronizar con Mailchimp, SendGrid o Brevo
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-2xl mb-2">📝</div>
                    <div class="font-semibold mb-1">Logging & Monitoreo</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Enviar eventos a Datadog, Sentry o New Relic
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-2xl mb-2">🔄</div>
                    <div class="font-semibold mb-1">CRM Personalizado</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Sincronizar datos con tu sistema interno
                    </div>
                </x-filament::card>
            </div>
        </x-filament::section>

        {{-- Buenas Prácticas --}}
        <x-filament::section>
            <x-slot name="heading">
                ⚙️ Reintentos y Buenas Prácticas
            </x-slot>

            <div class="space-y-4">

                <x-alert type="success" icon="check-circle">
                    <strong>Sistema de Reintentos Automático:</strong> Si tu endpoint falla, reintentaremos hasta 3
                    veces con delays exponenciales (1min, 5min, 15min)
                </x-alert>

                <x-alert type="info" icon="information-circle">
                    <strong>Responde Rápidamente:</strong> Tu endpoint debe responder con HTTP 200-299 en menos de 5
                    segundos
                </x-alert>

                <x-alert type="info" icon="arrow-path">
                    <strong>Procesa de Forma Asíncrona:</strong> Responde inmediatamente y procesa el webhook en segundo
                    plano (queue/workers)
                </x-alert>

                <x-alert type="warning" icon="document-duplicate">
                    <strong>Maneja Duplicados:</strong> Usa el campo <code class="font-mono">timestamp</code> como
                    identificador único para evitar procesar el mismo evento dos veces
                </x-alert>

            </div>
        </x-filament::section>

        {{-- Ejemplo n8n --}}
        <x-filament::section>
            <x-slot name="heading">
                🔧 Ejemplo Rápido: Configurar en n8n
            </x-slot>

            <ol class="space-y-3 text-sm list-decimal list-inside">
                <li>Crea un nuevo workflow en n8n y agrega un nodo <strong>Webhook</strong></li>
                <li>Copia la URL del webhook que te da n8n (ej: https://tu-n8n.com/webhook/abc123)</li>
                <li>Pégala en el campo URL al crear tu webhook aquí</li>
                <li>Agrega nodos adicionales en n8n (ej: Slack, Email, HTTP Request) para procesar los eventos</li>
                <li>Activa el workflow y listo! Recibirás eventos en tiempo real</li>
            </ol>
        </x-filament::section>

        {{-- Soporte --}}
        <x-filament::section>
            <x-slot name="heading">
                💬 ¿Necesitas Ayuda?
            </x-slot>
            <x-slot name="description">
                Si tienes dudas sobre la configuración de webhooks o necesitas ayuda con una integración
                específica, no dudes en contactarnos. Estamos aquí para ayudarte.
            </x-slot>
        </x-filament::section>
    </div>
</x-filament-panels::page>
