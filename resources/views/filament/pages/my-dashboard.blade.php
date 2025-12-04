<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Widgets --}}
        @foreach ($this->getWidgets() as $widget)
            @livewire($widget)
        @endforeach

        {{-- Acciones Rápidas --}}
        <x-filament::section>
            <x-slot name="heading">
                Acciones Rápidas
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Acceder a Chatwoot --}}
                @if (auth()->user()->activeChatwootAccount)
                    <x-filament::button tag="a" :href="auth()->user()->activeChatwootAccount->full_dashboard_url" target="_blank" icon="heroicon-o-arrow-top-right-on-square"
                        color="primary" size="lg">
                        Abrir Chatwoot
                    </x-filament::button>
                @endif

                {{-- Ver Mi Suscripción --}}
                <x-filament::button tag="a" :href="route('filament.admin.resources.subscriptions.index')" icon="heroicon-o-credit-card" color="success"
                    size="lg">
                    Mi Suscripción
                </x-filament::button>


                {{-- Mis Webhooks --}}
                <x-filament::button tag="a" :href="route('filament.admin.resources.user-webhooks.index')" icon="heroicon-o-link" color="info" size="lg">
                    Mis Webhooks
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Información del Plan --}}
        @if ($subscription = auth()->user()->activeSubscription)
            <x-filament::section>
                <x-slot name="heading">
                    Información de tu Plan
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Agentes --}}
                    <div>
                        <div class="text-sm text-gray-500 mb-2">Agentes</div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold">
                                {{ $chatwootMetrics['agents']['current'] ?? 0 }}
                            </span>
                            <span class="text-gray-400">
                                / {{ $subscription->plan->max_agents }}
                            </span>
                        </div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full"
                                style="width: {{ (($chatwootMetrics['agents']['current'] ?? 0) / $subscription->plan->max_agents) * 100 }}%">
                            </div>
                        </div>
                    </div>

                    {{-- Inboxes --}}
                    <div>
                        <div class="text-sm text-gray-500 mb-2">Inboxes</div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold">
                                {{ $chatwootMetrics['inboxes']['current'] ?? 0 }}
                            </span>
                            <span class="text-gray-400">
                                / {{ $subscription->plan->max_inboxes }}
                            </span>
                        </div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full"
                                style="width: {{ (($chatwootMetrics['inboxes']['current'] ?? 0) / $subscription->plan->max_inboxes) * 100 }}%">
                            </div>
                        </div>
                    </div>

                    {{-- Contactos --}}
                    <div>
                        <div class="text-sm text-gray-500 mb-2">Contactos</div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold">
                                {{ $chatwootMetrics['contacts']['current'] ?? 0 }}
                            </span>
                            <span class="text-gray-400">
                                / {{ $subscription->plan->max_contacts }}
                            </span>
                        </div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full"
                                style="width: {{ (($chatwootMetrics['contacts']['current'] ?? 0) / $subscription->plan->max_contacts) * 100 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
