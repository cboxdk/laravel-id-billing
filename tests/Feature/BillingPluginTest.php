<?php

declare(strict_types=1);

use Cbox\Billing\Client\BillingManagement;
use Cbox\Billing\Client\Testing\FakeManagementTransport;
use Cbox\Billing\Client\ValueObjects\Subscription;
use Cbox\Console\Kit\ConsoleManager;
use Cbox\Console\Kit\Contracts\CurrentContext;
use Cbox\Console\Kit\Facades\Console;

function bindBilling(?FakeManagementTransport $transport = null): void
{
    app()->instance(BillingManagement::class, new BillingManagement($transport ?? new FakeManagementTransport));
    app()->forgetInstance(ConsoleManager::class);
    Console::clearResolvedInstances();
}

function bindOrg(string $org): void
{
    app()->instance(CurrentContext::class, new class($org) implements CurrentContext
    {
        public function __construct(private string $org) {}

        public function organizationId(): ?string
        {
            return $this->org;
        }

        public function userId(): ?string
        {
            return 'user_1';
        }

        public function isAdmin(): bool
        {
            return true;
        }
    });
    app()->forgetInstance(ConsoleManager::class);
    Console::clearResolvedInstances();
}

it('registers the billing feature, active only when the billing client is bound', function (): void {
    expect(Console::featureActive('billing'))->toBeFalse();

    bindBilling();
    expect(Console::featureActive('billing'))->toBeTrue();
});

it('adds a gated Billing nav area with overview + invoices', function (): void {
    $area = collect(Console::nav()->areas())->firstWhere('key', 'billing');

    expect($area)->not->toBeNull();
    $routes = array_map(fn ($p) => $p->route, $area->pages());
    expect($routes)->toContain('billing.overview', 'billing.invoices')
        ->and($area->pages()[0]->feature)->toBe('billing');
});

it('renders a dashboard plan card for the current org when billing is wired', function (): void {
    bindBilling((new FakeManagementTransport)->withSubscription('org_1', new Subscription('pro', 'active')));
    bindOrg('org_1');

    $html = Console::slots()->render(ConsoleManager::DASHBOARD_CARDS);

    expect($html)->toContain('Current plan')->toContain('Pro');
});

it('renders no dashboard card when billing is not wired (self-hosted)', function (): void {
    expect(Console::slots()->render(ConsoleManager::DASHBOARD_CARDS))->toBe('');
});
