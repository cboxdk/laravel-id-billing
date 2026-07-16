<?php

declare(strict_types=1);

use Cbox\Billing\Client\BillingManagement;
use Cbox\Console\Kit\Facades\Console;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app', ['title' => 'Billing'])] class extends Component
{
    /** @return array<string, mixed> */
    public function with(): array
    {
        $billing = app(BillingManagement::class);
        $org = Console::context()->organizationId();

        return [
            'subscription' => $org !== null ? rescue(fn () => $billing->subscription($org), null, false) : null,
            'usage' => $org !== null ? rescue(fn () => $billing->usage($org), null, false) : null,
        ];
    }

    public function manage(BillingManagement $billing): void
    {
        $org = Console::context()->organizationId();
        if ($org === null) {
            return;
        }

        $session = rescue(fn () => $billing->createPortalSession($org, route('billing.overview')), null, false);
        if ($session !== null) {
            $this->redirect($session->url);
        }
    }
}; ?>

<div>
    <x-page-header title="Billing" subtitle="Your plan and usage. Change plan or manage payment in the billing portal." />

    @if (! $subscription)
        <div class="card p-10 text-center">
            <p class="font-semibold">No active subscription</p>
            <p class="mt-1 text-sm" style="color:var(--faint)">Choose a plan to unlock your organization's billing.</p>
            <button wire:click="manage" class="btn btn-primary btn-lg mt-4">Choose a plan</button>
        </div>
    @else
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="card p-5">
                <div class="text-sm" style="color:var(--muted)">Plan</div>
                <p class="mt-1 text-2xl font-semibold tracking-tight">{{ ucfirst($subscription->plan) }}</p>
                <span class="badge {{ $subscription->status === 'active' ? 'badge-success' : '' }} mt-2">{{ ucfirst($subscription->status) }}</span>
                @if ($subscription->renewsAt !== null)
                    <p class="mt-3 text-xs" style="color:var(--faint)">Renews {{ $subscription->renewsAt->format('M j, Y') }}</p>
                @endif
                @if ($subscription->cancelsAtPeriodEnd())
                    <p class="mt-1 text-xs" style="color:var(--danger)">Cancels at period end</p>
                @endif
                <button wire:click="manage" class="btn btn-primary w-full mt-4">Manage billing</button>
                <a href="{{ route('billing.invoices') }}" class="btn btn-ghost w-full mt-2">View invoices</a>
            </div>

            <div class="card lg:col-span-2">
                <div class="px-5 py-4 border-b" style="border-color:var(--border)">
                    <h3 class="font-semibold">Usage this period</h3>
                </div>
                <div class="p-5 space-y-4">
                    @forelse (($usage?->meters ?? []) as $meter => $m)
                        @php $pct = $m->allowance > 0 ? min(100, (int) round($m->used / $m->allowance * 100)) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span>{{ $meter }}</span>
                                <span class="mono" style="color:var(--muted)">{{ number_format($m->used) }} / {{ $m->allowance > 0 ? number_format($m->allowance) : '∞' }}</span>
                            </div>
                            <div class="mt-1.5 h-2 rounded-full overflow-hidden" style="background:var(--surface-2)">
                                <div class="h-2 rounded-full" style="width:{{ $pct }}%;background:{{ $m->overage > 0 ? 'var(--danger)' : 'var(--accent)' }}"></div>
                            </div>
                            @if ($m->overage > 0)
                                <p class="mt-1 text-xs" style="color:var(--danger)">{{ number_format($m->overage) }} over your limit</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm" style="color:var(--faint)">No metered usage this period.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
