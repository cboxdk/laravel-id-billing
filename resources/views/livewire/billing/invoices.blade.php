<?php

declare(strict_types=1);

use Cbox\Billing\Client\BillingManagement;
use Cbox\Console\Kit\Facades\Console;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app', ['title' => 'Invoices'])] class extends Component
{
    /** @return array<string, mixed> */
    public function with(): array
    {
        $org = Console::context()->organizationId();

        return [
            'invoices' => $org !== null
                ? rescue(fn () => app(BillingManagement::class)->invoices($org), [], false)
                : [],
        ];
    }
}; ?>

<div>
    <x-page-header title="Invoices" subtitle="Your organization's billing history." />

    <div class="card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b" style="border-color:var(--border)">
                    <th class="text-left px-5 py-3 font-medium" style="color:var(--muted)">Invoice</th>
                    <th class="text-left px-5 py-3 font-medium" style="color:var(--muted)">Date</th>
                    <th class="text-left px-5 py-3 font-medium" style="color:var(--muted)">Status</th>
                    <th class="text-right px-5 py-3 font-medium" style="color:var(--muted)">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $inv)
                    <tr class="border-b" style="border-color:var(--border)">
                        <td class="px-5 py-2.5 mono">{{ $inv->number }}</td>
                        <td class="px-5 py-2.5">{{ $inv->date?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-5 py-2.5"><span class="badge {{ $inv->isPaid() ? 'badge-success' : '' }}">{{ ucfirst($inv->status) }}</span></td>
                        <td class="px-5 py-2.5 text-right mono">{{ number_format($inv->amountMinor / 100, 2) }} {{ strtoupper($inv->currency) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-10 text-center text-sm" style="color:var(--faint)">No invoices yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
