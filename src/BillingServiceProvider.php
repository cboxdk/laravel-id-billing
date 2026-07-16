<?php

declare(strict_types=1);

namespace Cbox\Id\Billing;

use Cbox\Billing\Client\BillingManagement;
use Cbox\Console\Kit\Facades\Console;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\ServiceProvider;
use Livewire\Volt\Volt;
use Throwable;

/**
 * The Cbox ID billing plugin. Installed in the SaaS, it lights up a Billing area (plan,
 * invoices, usage-vs-limit), a dashboard "current plan" card, and gates — all through
 * console-kit, all gated on the `billing` feature, which is active only when the
 * billing client is wired. Removed, the console is the clean, billing-free app.
 */
final class BillingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'id-billing');
        Volt::mount([__DIR__.'/../resources/views/livewire']);
        $this->loadRoutesFrom(__DIR__.'/../routes/billing.php');

        // Billing is available only where the billing client is bound (the SaaS).
        Console::features()->register('billing', fn (): bool => $this->app->bound(BillingManagement::class));

        // Nav — pages gated on the feature, so nothing shows on a self-hosted install.
        Console::nav()->area('billing', 'Billing', 'clients', 65)
            ->page('billing.overview', 'Overview', feature: 'billing', order: 10)
            ->page('billing.invoices', 'Invoices', feature: 'billing', order: 20);

        // Overview dashboard card — the current plan. Empty (nothing rendered) unless
        // billing is wired and the current org has a subscription.
        Console::dashboardCard(fn (): string => $this->planCard(), 5);
    }

    private function planCard(): string
    {
        if (! $this->app->bound(BillingManagement::class)) {
            return '';
        }

        $org = Console::context()->organizationId();
        if ($org === null) {
            return '';
        }

        try {
            $subscription = $this->app->make(BillingManagement::class)->subscription($org);
        } catch (Throwable) {
            return '';
        }

        if ($subscription === null) {
            return '';
        }

        return $this->app->make(Factory::class)
            ->make('id-billing::components.plan-card', ['subscription' => $subscription])
            ->render();
    }
}
