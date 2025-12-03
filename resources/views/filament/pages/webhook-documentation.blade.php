<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Introducción --}}
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg rounded-lg p-6">
            <div class="flex items-start gap-4">
                <div class="text-4xl">🔗</div>
                <div>
                    <h2 class="text-2xl font-bold mb-2">Webhooks - Notificaciones en Tiempo Real</h2>
                    <p class="text-blue-50">
                        Recibe notificaciones instantáneas cuando ocurran eventos importantes en tu cuenta.
                        Integra fácilmente con n8n, Zapier, Make.com o tu propio sistema.
                    </p>
                </div>
            </div>
        </div>

        {{-- Eventos Disponibles --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                <span class="text-2xl">🚀</span>
                Eventos Disponibles
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border border-green-200 dark:border-green-800 rounded-lg p-4 bg-green-50 dark:bg-green-900/20">
                    <div class="font-semibold text-green-700 dark:text-green-400 mb-2">📊 Cuenta</div>
                    <ul class="space-y-1 text-sm">
                        <li><code class="bg-white dark:bg-gray-900 px-2 py-1 rounded">account.created</code> - Cuenta creada</li>
                        <li><code class="bg-white dark:bg-gray-900 px-2 py-1 rounded">account.suspended</code> - Cuenta suspendida</li>
                        <li><code class="bg-white dark:bg-gray-900 px-2 py-1 rounded">account.activated</code> - Cuenta reactivada</li>
                    </ul>
                </div>

                <div class="border border-blue-200 dark:border-blue-800 rounded-lg p-4 bg-blue-50 dark:bg-blue-900/20">
                    <div class="font-semibold text-blue-700 dark:text-blue-400 mb-2">💳 Pagos</div>
                    <ul class="space-y-1 text-sm">
                        <li><code class="bg-white dark:bg-gray-900 px-2 py-1 rounded">payment.approved</code> - Pago aprobado</li>
                        <li><code class="bg-white dark:bg-gray-900 px-2 py-1 rounded">payment.failed</code> - Pago fallido</li>
                    </ul>
                </div>

                <div class="border border-purple-200 dark:border-purple-800 rounded-lg p-4 bg-purple-50 dark:bg-purple-900/20">
                    <div class="font-semibold text-purple-700 dark:text-purple-400 mb-2">📅 Suscripciones</div>
                    <ul class="space-y-1 text-sm">
                        <li><code class="bg-white dark:bg-gray-900 px-2 py-1 rounded">subscription.activated</code> - Activada</li>
                        <li><code class="bg-white dark:bg-gray-900 px-2 py-1 rounded">subscription.cancelled</code> - Cancelada</li>
                        <li><code class="bg-white dark:bg-gray-900 px-2 py-1 rounded">subscription.renewed</code> - Renovada</li>
                    </ul>
                </div>

                <div class="border border-orange-200 dark:border-orange-800 rounded-lg p-4 bg-orange-50 dark:bg-orange-900/20">
                    <div class="font-semibold text-orange-700 dark:text-orange-400 mb-2">⚠️ Límites</div>
                    <ul class="space-y-1 text-sm">
                        <li><code class="bg-white dark:bg-gray-900 px-2 py-1 rounded">plan.limit_exceeded</code> - Límite excedido</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Formato del Payload --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-bold mb-3 flex items-center gap-2">
                <span class="text-2xl">📦</span>
                Formato del Payload
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Todos los webhooks envían una petición POST con el siguiente formato JSON:
            </p>
            <div class="bg-gray-100 dark:bg-gray-900 rounded-lg p-4 overflow-x-auto">
                <pre class="text-sm"><code>{
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
                    <span class="font-mono bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">event</span>
                    <span class="text-gray-600 dark:text-gray-400">Tipo de evento disparado</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="font-mono bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">timestamp</span>
                    <span class="text-gray-600 dark:text-gray-400">Fecha/hora en formato ISO 8601</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="font-mono bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">signature</span>
                    <span class="text-gray-600 dark:text-gray-400">HMAC SHA-256 para verificación</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="font-mono bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">data</span>
                    <span class="text-gray-600 dark:text-gray-400">Datos específicos del evento</span>
                </div>
            </div>
        </div>

        {{-- Verificar Firma --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-bold mb-3 flex items-center gap-2">
                <span class="text-2xl">🔐</span>
                Verificar Firma (Recomendado)
            </h3>
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-4">
                <div class="flex items-start gap-2">
                    <span class="text-xl">⚡</span>
                    <div class="text-sm">
                        <strong>Importante:</strong> Siempre configura un secret y verifica la firma para asegurar
                        que los webhooks provienen realmente de tu sistema.
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                {{-- Node.js / n8n --}}
                <div>
                    <div class="text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Node.js / n8n</div>
                    <div class="bg-gray-100 dark:bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-sm"><code>const crypto = require('crypto');

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
  // Procesar el evento...
} else {
  console.log('❌ Firma inválida');
}</code></pre>
                    </div>
                </div>

                {{-- PHP --}}
                <div>
                    <div class="text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">PHP</div>
                    <div class="bg-gray-100 dark:bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-sm"><code>function verifyWebhook($payload, $receivedSignature, $secret) {
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
} else {
    // ❌ Firma inválida
}</code></pre>
                    </div>
                </div>

                {{-- Python --}}
                <div>
                    <div class="text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Python</div>
                    <div class="bg-gray-100 dark:bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-sm"><code>import hmac
import hashlib
import json

def verify_webhook(payload, received_signature, secret):
    signature = hmac.new(
        secret.encode('utf-8'),
        json.dumps(payload['data']).encode('utf-8'),
        hashlib.sha256
    ).hexdigest()

    return hmac.compare_digest(signature, received_signature)

# Uso
if verify_webhook(payload, payload['signature'], 'tu_secret'):
    print('✅ Webhook válido')
else:
    print('❌ Firma inválida')</code></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- Casos de Uso --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                <span class="text-2xl">💡</span>
                Casos de Uso e Integraciones
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="text-2xl mb-2">📱</div>
                    <div class="font-semibold mb-1">WhatsApp / SMS</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Notificar por WhatsApp usando n8n + Twilio o Evolution API
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="text-2xl mb-2">📊</div>
                    <div class="font-semibold mb-1">Dashboard Personalizado</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Actualizar métricas en tiempo real en tu panel de control
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="text-2xl mb-2">💬</div>
                    <div class="font-semibold mb-1">Notificaciones de Equipo</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Enviar alertas a Slack, Discord o Telegram
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="text-2xl mb-2">📧</div>
                    <div class="font-semibold mb-1">Email Marketing</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Sincronizar con Mailchimp, SendGrid o Brevo
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="text-2xl mb-2">📝</div>
                    <div class="font-semibold mb-1">Logging & Monitoreo</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Enviar eventos a Datadog, Sentry o New Relic
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="text-2xl mb-2">🔄</div>
                    <div class="font-semibold mb-1">CRM Personalizado</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Sincronizar datos con tu sistema interno
                    </div>
                </div>
            </div>
        </div>

        {{-- Reintentos y Buenas Prácticas --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                <span class="text-2xl">⚙️</span>
                Reintentos y Buenas Prácticas
            </h3>
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <span class="text-green-500 text-xl">✓</span>
                    <div>
                        <div class="font-semibold">Sistema de Reintentos Automático</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Si tu endpoint falla, reintentaremos hasta 3 veces con delays exponenciales (1min, 5min, 15min)
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="text-green-500 text-xl">✓</span>
                    <div>
                        <div class="font-semibold">Responde Rápidamente</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Tu endpoint debe responder con HTTP 200-299 en menos de 5 segundos
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="text-green-500 text-xl">✓</span>
                    <div>
                        <div class="font-semibold">Procesa de Forma Asíncrona</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Responde inmediatamente y procesa el webhook en segundo plano (queue/workers)
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="text-green-500 text-xl">✓</span>
                    <div>
                        <div class="font-semibold">Maneja Duplicados</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Usa el campo <code class="bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">timestamp</code>
                            como identificador único para evitar procesar el mismo evento dos veces
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ejemplo con n8n --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                <span class="text-2xl">🔧</span>
                Ejemplo Rápido: Configurar en n8n
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex items-start gap-2">
                    <span class="font-bold text-blue-500">1.</span>
                    <div>Crea un nuevo workflow en n8n y agrega un nodo <strong>Webhook</strong></div>
                </div>
                <div class="flex items-start gap-2">
                    <span class="font-bold text-blue-500">2.</span>
                    <div>Copia la URL del webhook que te da n8n (ej: https://tu-n8n.com/webhook/abc123)</div>
                </div>
                <div class="flex items-start gap-2">
                    <span class="font-bold text-blue-500">3.</span>
                    <div>Pégala en el campo URL al crear tu webhook aquí</div>
                </div>
                <div class="flex items-start gap-2">
                    <span class="font-bold text-blue-500">4.</span>
                    <div>Agrega nodos adicionales en n8n (ej: Slack, Email, HTTP Request) para procesar los eventos</div>
                </div>
                <div class="flex items-start gap-2">
                    <span class="font-bold text-blue-500">5.</span>
                    <div>Activa el workflow y listo! Recibirás eventos en tiempo real</div>
                </div>
            </div>
        </div>

        {{-- Soporte --}}
        <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg rounded-lg p-6">
            <div class="flex items-start gap-4">
                <div class="text-4xl">💬</div>
                <div>
                    <h3 class="text-xl font-bold mb-2">¿Necesitas Ayuda?</h3>
                    <p class="text-purple-50 text-sm">
                        Si tienes dudas sobre la configuración de webhooks o necesitas ayuda con una integración
                        específica, no dudes en contactarnos. Estamos aquí para ayudarte.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
