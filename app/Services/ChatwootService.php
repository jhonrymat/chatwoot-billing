<?php

// ============================================
// app/Services/ChatwootService.php
// ============================================

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatwootService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout = 30;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('chatwoot.url'), '/');
        $this->apiKey = config('chatwoot.api_key');

        if (!$this->baseUrl || !$this->apiKey) {
            throw new \RuntimeException('Chatwoot configuration is missing. Check CHATWOOT_URL and CHATWOOT_API_KEY in .env');
        }
    }

    /**
     * Crear una nueva cuenta en Chatwoot
     */
    public function createAccount(string $name, string $locale = 'es'): array
    {
        try {
            $response = $this->makeRequest('POST', '/platform/api/v1/accounts', [
                'name' => $name,
                'locale' => $locale,
            ]);

            Log::info('Chatwoot account created', [
                'account_id' => $response['id'],
                'account_name' => $name,
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create Chatwoot account', [
                'name' => $name,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Crear usuario administrador en una cuenta
     */
    public function createUser(int $accountId, array $userData): array
    {
        try {
            $response = $this->makeRequest('POST', "/platform/api/v1/accounts/{$accountId}/account_users", [
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'role' => $userData['role'] ?? 'administrator',
                'availability_status' => 'online',
            ]);

            Log::info('Chatwoot user created', [
                'account_id' => $accountId,
                'user_email' => $userData['email'],
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create Chatwoot user', [
                'account_id' => $accountId,
                'email' => $userData['email'],
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Obtener información de una cuenta
     */
    public function getAccount(int $accountId): array
    {
        try {
            return $this->makeRequest('GET', "/platform/api/v1/accounts/{$accountId}");
        } catch (\Exception $e) {
            Log::error('Failed to get Chatwoot account', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Actualizar una cuenta
     */
    public function updateAccount(int $accountId, array $data): array
    {
        try {
            return $this->makeRequest('PATCH', "/platform/api/v1/accounts/{$accountId}", $data);
        } catch (\Exception $e) {
            Log::error('Failed to update Chatwoot account', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Obtener agentes de una cuenta
     */
    public function getAgents(int $accountId): array
    {
        try {
            return $this->makeRequest('GET', "/platform/api/v1/accounts/{$accountId}/account_users");
        } catch (\Exception $e) {
            Log::error('Failed to get agents', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Obtener inboxes de una cuenta
     */
    public function getInboxes(int $accountId): array
    {
        try {
            // Usar API de cuenta específica (requiere token de usuario)
            return $this->makeRequest('GET', "/api/v1/accounts/{$accountId}/inboxes");
        } catch (\Exception $e) {
            Log::error('Failed to get inboxes', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);
            // Retornar array vacío si falla (puede ser por permisos)
            return [];
        }
    }

    /**
     * Crear un inbox web en la cuenta
     */
    public function createWebInbox(int $accountId, string $name, string $websiteUrl): array
    {
        try {
            return $this->makeRequest('POST', "/api/v1/accounts/{$accountId}/inboxes", [
                'name' => $name,
                'channel' => [
                    'type' => 'web',
                    'website_url' => $websiteUrl,
                    'welcome_title' => '¡Hola! 👋',
                    'welcome_tagline' => '¿En qué podemos ayudarte?',
                    'widget_color' => '#1f93ff',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create inbox', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Obtener contactos de una cuenta
     */
    public function getContacts(int $accountId, int $page = 1): array
    {
        try {
            return $this->makeRequest('GET', "/api/v1/accounts/{$accountId}/contacts", [
                'page' => $page,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get contacts', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);
            return ['meta' => ['count' => 0], 'payload' => []];
        }
    }

    /**
     * Obtener métricas/reportes de una cuenta
     */
    public function getAccountMetrics(int $accountId, string $type = 'account', array $params = []): array
    {
        try {
            $defaultParams = [
                'metric' => 'conversations_count',
                'type' => $type,
                'since' => now()->subDays(30)->timestamp,
                'until' => now()->timestamp,
            ];

            $params = array_merge($defaultParams, $params);

            return $this->makeRequest('GET', "/api/v1/accounts/{$accountId}/reports", $params);
        } catch (\Exception $e) {
            Log::error('Failed to get metrics', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Obtener estadísticas de conversaciones
     */
    public function getConversationStats(int $accountId): array
    {
        try {
            $response = $this->makeRequest('GET', "/api/v1/accounts/{$accountId}/conversations", [
                'status' => 'all',
                'page' => 1,
            ]);

            // Chatwoot retorna las conversaciones con metadata
            return [
                'total' => $response['meta']['all_count'] ?? 0,
                'open' => $response['meta']['open_count'] ?? 0,
                'resolved' => $response['meta']['resolved_count'] ?? 0,
                'pending' => $response['meta']['pending_count'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get conversation stats', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);
            return [
                'total' => 0,
                'open' => 0,
                'resolved' => 0,
                'pending' => 0,
            ];
        }
    }

    /**
     * Suspender una cuenta (cambiar estado)
     */
    public function suspendAccount(int $accountId): bool
    {
        try {
            $this->updateAccount($accountId, [
                'status' => 'suspended',
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to suspend account', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Activar una cuenta
     */
    public function activateAccount(int $accountId): bool
    {
        try {
            $this->updateAccount($accountId, [
                'status' => 'active',
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to activate account', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Verificar si una cuenta existe y está activa
     */
    public function accountExists(int $accountId): bool
    {
        try {
            $account = $this->getAccount($accountId);
            return !empty($account['id']);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtener todas las métricas necesarias para el dashboard
     */
    public function getComprehensiveMetrics(int $accountId): array
    {
        try {
            // Obtener datos en paralelo
            $agents = $this->getAgents($accountId);
            $inboxes = $this->getInboxes($accountId);
            $contacts = $this->getContacts($accountId);
            $conversationStats = $this->getConversationStats($accountId);

            return [
                'conversations' => [
                    'total' => $conversationStats['total'],
                    'open' => $conversationStats['open'],
                    'resolved' => $conversationStats['resolved'],
                    'pending' => $conversationStats['pending'],
                ],
                'agents' => [
                    'total' => count($agents),
                    'active' => count(array_filter($agents, fn($a) => $a['availability_status'] === 'online')),
                ],
                'inboxes' => [
                    'total' => count($inboxes),
                ],
                'contacts' => [
                    'total' => $contacts['meta']['count'] ?? 0,
                ],
                'response_times' => [
                    'first_response' => null, // Requiere endpoints específicos
                    'resolution' => null,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get comprehensive metrics', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);

            return [
                'conversations' => ['total' => 0, 'open' => 0, 'resolved' => 0, 'pending' => 0],
                'agents' => ['total' => 0, 'active' => 0],
                'inboxes' => ['total' => 0],
                'contacts' => ['total' => 0],
                'response_times' => ['first_response' => null, 'resolution' => null],
            ];
        }
    }

    /**
     * Realizar request HTTP a la API de Chatwoot
     */
    protected function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;

        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'api_access_token' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->$method($url, $data);

        if (!$response->successful()) {
            throw new \RuntimeException(
                "Chatwoot API request failed: {$response->status()} - {$response->body()}"
            );
        }

        return $response->json() ?? [];
    }

    /**
     * Construir URL del dashboard de una cuenta
     */
    public function getDashboardUrl(int $accountId): string
    {
        return "{$this->baseUrl}/app/accounts/{$accountId}/dashboard";
    }

    /**
     * Verificar conectividad con Chatwoot
     */
    public function testConnection(): bool
    {
        try {
            // Intentar listar cuentas (endpoint de platform)
            $this->makeRequest('GET', '/platform/api/v1/accounts');
            return true;
        } catch (\Exception $e) {
            Log::error('Chatwoot connection test failed', [
                'url' => $this->baseUrl,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Obtener límites actuales vs plan
     */
    public function checkPlanLimits(int $accountId, array $planLimits): array
    {
        try {
            $agents = $this->getAgents($accountId);
            $inboxes = $this->getInboxes($accountId);
            $contacts = $this->getContacts($accountId);

            return [
                'agents' => [
                    'current' => count($agents),
                    'limit' => $planLimits['max_agents'],
                    'available' => $planLimits['max_agents'] - count($agents),
                    'exceeded' => count($agents) > $planLimits['max_agents'],
                ],
                'inboxes' => [
                    'current' => count($inboxes),
                    'limit' => $planLimits['max_inboxes'],
                    'available' => $planLimits['max_inboxes'] - count($inboxes),
                    'exceeded' => count($inboxes) > $planLimits['max_inboxes'],
                ],
                'contacts' => [
                    'current' => $contacts['meta']['count'] ?? 0,
                    'limit' => $planLimits['max_contacts'],
                    'available' => $planLimits['max_contacts'] - ($contacts['meta']['count'] ?? 0),
                    'exceeded' => ($contacts['meta']['count'] ?? 0) > $planLimits['max_contacts'],
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Failed to check plan limits', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);

            return [
                'agents' => ['current' => 0, 'limit' => $planLimits['max_agents'], 'available' => $planLimits['max_agents'], 'exceeded' => false],
                'inboxes' => ['current' => 0, 'limit' => $planLimits['max_inboxes'], 'available' => $planLimits['max_inboxes'], 'exceeded' => false],
                'contacts' => ['current' => 0, 'limit' => $planLimits['max_contacts'], 'available' => $planLimits['max_contacts'], 'exceeded' => false],
            ];
        }
    }
}
