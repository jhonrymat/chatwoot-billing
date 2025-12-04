<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Widgets como componentes Livewire --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @livewire(\App\Filament\Widgets\MySubscriptionWidget::class)
            @livewire(\App\Filament\Widgets\MyChatwootMetrics::class)
        </div>

        {{-- Acciones Rápidas --}}
        <x-filament::section>
            <x-slot name="heading">
                Acciones Rápidas
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if(auth()->user()->activeChatwootAccount)
                    <x-filament::button
                        :href="auth()->user()->activeChatwootAccount->full_dashboard_url"
                        target="_blank"
                        icon="heroicon-o-arrow-top-right-on-square"
                        color="primary"
                        size="lg"
                        tag="a"
                    >
                        Abrir Chatwoot
                    </x-filament::button>
                @endif

                <x-filament::button
                    href="{{ route('filament.admin.pages.my-billing') }}"
                    icon="heroicon-o-credit-card"
                    color="success"
                    size="lg"
                    tag="a"
                >
                    Mi Suscripción
                </x-filament::button>

                <x-filament::button
                    href="{{ route('filament.admin.pages.my-chatwoot-dashboard') }}"
                    icon="heroicon-o-chart-bar"
                    color="info"
                    size="lg"
                    tag="a"
                >
                    Ver Métricas
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Información del Plan - Uso de computed property --}}
        @if($subscription = auth()->user()->activeSubscription)
            <x-filament::section>
                <x-slot name="heading">
                    Uso de Recursos de tu Plan
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @php
                        $metrics = $this->chatwootMetrics;
                    @endphp

                    {{-- Agentes --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Agentes</span>
                            <span class="text-sm text-gray-500">
                                {{ $metrics['agents']['current'] }} / {{ $metrics['agents']['limit'] }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            @php
                                $percentage = $metrics['agents']['limit'] > 0
                                    ? min(($metrics['agents']['current'] / $metrics['agents']['limit']) * 100, 100)
                                    : 0;
                                $color = $percentage >= 90 ? 'bg-red-600' : ($percentage >= 70 ? 'bg-yellow-600' : 'bg-blue-600');
                            @endphp
                            <div class="{{ $color }} h-3 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                        </div>
                        @if($percentage >= 90)
                            <p class="text-xs text-red-600">⚠️ Límite casi alcanzado</p>
                        @endif
                    </div>

                    {{-- Inboxes --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Inboxes</span>
                            <span class="text-sm text-gray-500">
                                {{ $metrics['inboxes']['current'] }} / {{ $metrics['inboxes']['limit'] }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            @php
                                $percentage = $metrics['inboxes']['limit'] > 0
                                    ? min(($metrics['inboxes']['current'] / $metrics['inboxes']['limit']) * 100, 100)
                                    : 0;
                                $color = $percentage >= 90 ? 'bg-red-600' : ($percentage >= 70 ? 'bg-yellow-600' : 'bg-green-600');
                            @endphp
                            <div class="{{ $color }} h-3 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                        </div>
                        @if($percentage >= 90)
                            <p class="text-xs text-red-600">⚠️ Límite casi alcanzado</p>
                        @endif
                    </div>

                    {{-- Contactos --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Contactos</span>
                            <span class="text-sm text-gray-500">
                                {{ number_format($metrics['contacts']['current']) }} / {{ number_format($metrics['contacts']['limit']) }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            @php
                                $percentage = $metrics['contacts']['limit'] > 0
                                    ? min(($metrics['contacts']['current'] / $metrics['contacts']['limit']) * 100, 100)
                                    : 0;
                                $color = $percentage >= 90 ? 'bg-red-600' : ($percentage >= 70 ? 'bg-yellow-600' : 'bg-purple-600');
                            @endphp
                            <div class="{{ $color }} h-3 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                        </div>
                        @if($percentage >= 90)
                            <p class="text-xs text-red-600">⚠️ Límite casi alcanzado</p>
                        @endif
                    </div>
                </div>

                {{-- Alerta si excede algún límite --}}
                @php
                    $exceeded = collect($metrics)->filter(fn($m) => $m['limit'] > 0 && $m['current'] > $m['limit'])->isNotEmpty();
                @endphp

                @if($exceeded)
                    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Has superado los límites de tu plan actual.
                                    <a href="{{ route('filament.admin.pages.upgrade-plan') }}" class="font-medium underline hover:text-yellow-600">
                                        Considera mejorar tu plan
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </x-filament::section>
        @endif

        {{-- Actividad Reciente --}}
        @livewire(\App\Filament\Widgets\MyRecentActivity::class)
    </div>
</x-filament-panels::page>
