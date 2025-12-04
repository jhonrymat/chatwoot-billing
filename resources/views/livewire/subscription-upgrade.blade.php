<div class="space-y-6">
    <div class="text-lg font-semibold mb-4">
        Mejorar mi Plan
    </div>

    @if ($currentPlan)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Plan Actual: {{ $currentPlan->name }}
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        Precio: ${{ number_format($currentPlan->price, 0) }} COP /
                        {{ $currentPlan->billing_cycle === 'monthly' ? 'mes' : 'año' }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($availablePlans as $plan)
            <div class="border rounded-lg p-6 cursor-pointer transition-all hover:shadow-lg {{ $selectedPlan?->id === $plan->id ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-200' }}"
                wire:click="selectPlan({{ $plan->id }})">
                @if ($plan->is_popular)
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 mb-4">
                        Popular
                    </span>
                @endif

                <h3 class="text-xl font-bold mb-2">{{ $plan->name }}</h3>
                <div class="text-3xl font-bold mb-4">
                    ${{ number_format($plan->price, 0) }}
                    <span class="text-sm text-gray-500">/
                        {{ $plan->billing_cycle === 'monthly' ? 'mes' : 'año' }}</span>
                </div>

                <ul class="space-y-2 mb-6">
                    @foreach ($plan->features as $feature)
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm">{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>

                @if ($selectedPlan?->id === $plan->id)
                    <div class="text-center">
                        <svg class="h-8 w-8 text-blue-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm text-blue-600 mt-2">Seleccionado</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if ($selectedPlan)
        <div class="flex justify-end">
            <button wire:click="upgrade"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Continuar al Pago
            </button>
        </div>
    @endif
</div>
