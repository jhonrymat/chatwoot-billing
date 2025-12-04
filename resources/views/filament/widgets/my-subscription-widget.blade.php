<x-filament-widgets::widget>
    @php
        $subscription = $this->getSubscription();
    @endphp

    @if($subscription)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between">
                    <span>Tu Suscripción Actual</span>
                    @if($subscription->is_active)
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                            Activa
                        </span>
                    @endif
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ $subscription->plan->name }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $subscription->plan->description }}
                        </p>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Precio:</span>
                            <span class="font-semibold">
                                ${{ number_format($subscription->plan->price, 0) }} COP
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ciclo:</span>
                            <span>{{ $subscription->plan->billing_cycle === 'monthly' ? 'Mensual' : 'Anual' }}</span>
                        </div>
                        @if($subscription->next_billing_date)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Próxima renovación:</span>
                                <span class="font-semibold">
                                    {{ $subscription->next_billing_date->format('d/m/Y') }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Días restantes:</span>
                                <span>
                                    {{ $subscription->next_billing_date->diffInDays(now()) }} días
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col justify-center space-y-3">
                    <x-filament::button
                        href="{{ route('filament.admin.pages.upgrade-plan') }}"
                        icon="heroicon-o-arrow-trending-up"
                        color="success"
                        size="lg"
                        tag="a"
                    >
                        Mejorar Plan
                    </x-filament::button>

                    <x-filament::button
                        href="{{ route('filament.admin.pages.my-billing') }}"
                        icon="heroicon-o-credit-card"
                        color="primary"
                        outlined
                        tag="a"
                    >
                        Ver Facturación
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>
    @else
        <x-filament::section>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">No tienes suscripción activa</h3>
                <p class="mt-1 text-sm text-gray-500">Selecciona un plan para comenzar</p>
                <div class="mt-6">
                    <x-filament::button
                        href="{{ route('plans.index') }}"
                        icon="heroicon-o-plus"
                        tag="a"
                    >
                        Ver Planes Disponibles
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>
