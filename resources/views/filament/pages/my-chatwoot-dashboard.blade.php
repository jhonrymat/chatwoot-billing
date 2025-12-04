<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Botón de actualizar --}}
        <div class="flex justify-end">
            <x-filament::button
                wire:click="refreshMetrics"
                icon="heroicon-o-arrow-path"
                color="primary"
            >
                Actualizar Métricas
            </x-filament::button>
        </div>

        {{-- Métricas principales --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            {{-- Conversaciones --}}
            <x-filament::section>
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-600">
                        {{ $metrics['conversations']['total'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500 mt-2">Total Conversaciones</div>
                    <div class="mt-4 space-y-1 text-xs">
                        <div class="flex justify-between">
                            <span>Abiertas:</span>
                            <span class="font-semibold">{{ $metrics['conversations']['open'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Resueltas:</span>
                            <span class="font-semibold">{{ $metrics['conversations']['resolved'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Pendientes:</span>
                            <span class="font-semibold">{{ $metrics['conversations']['pending'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </x-filament::section>

            {{-- Agentes --}}
            <x-filament::section>
                <div class="text-center">
                    <div class="text-4xl font-bold text-green-600">
                        {{ $metrics['agents']['total'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500 mt-2">Agentes</div>
                    <div class="mt-4 text-xs">
                        <div>Activos: <span class="font-semibold">{{ $metrics['agents']['active'] ?? 0 }}</span></div>
                    </div>
                </div>
            </x-filament::section>

            {{-- Inboxes --}}
            <x-filament::section>
                <div class="text-center">
                    <div class="text-4xl font-bold text-yellow-600">
                        {{ $metrics['inboxes']['total'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500 mt-2">Inboxes</div>
                </div>
            </x-filament::section>

            {{-- Contactos --}}
            <x-filament::section>
                <div class="text-center">
                    <div class="text-4xl font-bold text-purple-600">
                        {{ $metrics['contacts']['total'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500 mt-2">Contactos</div>
                </div>
            </x-filament::section>
        </div>

        {{-- Límites del Plan --}}
        @if($limits)
            <x-filament::section>
                <x-slot name="heading">
                    Uso de Recursos
                </x-slot>

                <div class="space-y-6">
                    {{-- Agentes --}}
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span>Agentes</span>
                            <span class="{{ $limits['agents']['exceeded'] ? 'text-red-600 font-bold' : '' }}">
                                {{ $limits['agents']['current'] }} / {{ $limits['agents']['limit'] }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div
                                class="h-3 rounded-full {{ $limits['agents']['exceeded'] ? 'bg-red-600' : 'bg-green-600' }}"
                                style="width: {{ min(($limits['agents']['current'] / $limits['agents']['limit']) * 100, 100) }}%"
                            ></div>
                        </div>
                        @if($limits['agents']['exceeded'])
                            <p class="text-xs text-red-600 mt-1">⚠️ Has excedido el límite de agentes</p>
                        @endif
                    </div>

                    {{-- Inboxes --}}
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span>Inboxes</span>
                            <span class="{{ $limits['inboxes']['exceeded'] ? 'text-red-600 font-bold' : '' }}">
                                {{ $limits['inboxes']['current'] }} / {{ $limits['inboxes']['limit'] }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div
                                class="h-3 rounded-full {{ $limits['inboxes']['exceeded'] ? 'bg-red-600' : 'bg-yellow-600' }}"
                                style="width: {{ min(($limits['inboxes']['current'] / $limits['inboxes']['limit']) * 100, 100) }}%"
                            ></div>
                        </div>
                        @if($limits['inboxes']['exceeded'])
                            <p class="text-xs text-red-600 mt-1">⚠️ Has excedido el límite de inboxes</p>
                        @endif
                    </div>

                    {{-- Contactos --}}
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span>Contactos</span>
                            <span class="{{ $limits['contacts']['exceeded'] ? 'text-red-600 font-bold' : '' }}">
                                {{ number_format($limits['contacts']['current']) }} / {{ number_format($limits['contacts']['limit']) }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div
                                class="h-3 rounded-full {{ $limits['contacts']['exceeded'] ? 'bg-red-600' : 'bg-blue-600' }}"
                                style="width: {{ min(($limits['contacts']['current'] / $limits['contacts']['limit']) * 100, 100) }}%"
                            ></div>
                        </div>
                        @if($limits['contacts']['exceeded'])
                            <p class="text-xs text-red-600 mt-1">⚠️ Has excedido el límite de contactos</p>
                        @endif
                    </div>
                </div>

                @if(collect($limits)->pluck('exceeded')->contains(true))
                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    Has excedido los límites de tu plan
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Considera mejorar tu plan para seguir agregando recursos.</p>
                                </div>
                                <div class="mt-4">
                                    <x-filament::button
                                        :href="route('filament.admin.pages.upgrade-plan')"
                                        color="warning"
                                        size="sm"
                                    >
                                        Ver Planes Disponibles
                                    </x-filament::button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </x-filament::section>
        @endif

        {{-- Acceso directo a Chatwoot --}}
        <x-filament::section>
            <x-slot name="heading">
                Acceso a Chatwoot
            </x-slot>

            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium">Ir a tu Dashboard de Chatwoot</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Gestiona tus conversaciones y configura tu sistema
                    </p>
                </div>
                <x-filament::button
                    :href="auth()->user()->activeChatwootAccount->full_dashboard_url"
                    target="_blank"
                    icon="heroicon-o-arrow-top-right-on-square"
                    color="primary"
                    size="lg"
                >
                    Abrir Chatwoot
                </x-filament::button>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
