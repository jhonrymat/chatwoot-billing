<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Suscripción Actual --}}
        @if ($subscription)
            <x-filament::section>
                <x-slot name="heading">
                    Suscripción Actual
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">{{ $subscription->plan->name }}</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Precio:</dt>
                                <dd class="font-semibold">${{ number_format($subscription->plan->price, 0) }} COP</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Ciclo:</dt>
                                <dd>{{ $subscription->plan->billing_cycle === 'monthly' ? 'Mensual' : 'Anual' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Estado:</dt>
                                <dd>
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full {{ $subscription->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </dd>
                            </div>
                            @if ($subscription->next_billing_date)
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Próxima renovación:</dt>
                                    <dd class="font-semibold">{{ $subscription->next_billing_date }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <div class="flex flex-col justify-center space-y-3">
                        <x-filament::button tag="a" :href="route('filament.admin.pages.upgrade-plan')" icon="heroicon-o-arrow-trending-up"
                            color="success">
                            Mejorar Plan
                        </x-filament::button>

                        <x-filament::button wire:click="cancelSubscription" icon="heroicon-o-x-circle" color="danger"
                            wire:confirm="¿Estás seguro de cancelar tu suscripción?">
                            Cancelar Suscripción
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        @else
            <x-filament::section>
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No tienes suscripción activa</h3>
                    <p class="mt-1 text-sm text-gray-500">Comienza seleccionando un plan</p>
                    <div class="mt-6">
                        <x-filament::button :href="route('plans.index')" icon="heroicon-o-plus">
                            Ver Planes
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Historial de Pagos --}}
        <x-filament::section>
            <x-slot name="heading">
                Historial de Pagos
            </x-slot>

            @if ($payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $payment->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        ${{ number_format($payment->amount, 0) }} {{ $payment->currency }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full {{ $payment->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $payment->subscription->plan->name ?? 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <p>No hay pagos registrados</p>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
