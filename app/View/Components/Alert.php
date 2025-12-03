<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public function __construct(
        public string $type = 'info', // success, danger, warning, info, gray
        public ?string $icon = null,
        public bool $dismissible = false
    ) {}

    protected function getColorClasses(): string
    {
        return match ($this->type) {
            'success' => 'fi-alert-success bg-success-50 text-success-700 dark:bg-success-500/10',
            'danger'  => 'fi-alert-danger bg-danger-50 text-danger-700 dark:bg-danger-500/10',
            'warning' => 'fi-alert-warning bg-warning-50 text-warning-700 dark:bg-warning-500/10',
            'info'    => 'fi-alert-info bg-info-50 text-info-700 dark:bg-info-500/10',
            'gray'    => 'fi-alert-gray bg-gray-50 text-gray-700 dark:bg-gray-500/10',
            default   => 'fi-alert-info bg-custom-50 text-custom-700 dark:bg-custom-500/10',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.alert', [
            'colorClasses' => $this->getColorClasses(),
        ]);
    }
}
