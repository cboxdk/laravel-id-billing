@php
    /** @var \Cbox\Billing\Client\Subscription $subscription */
    $active = $subscription->status === 'active';
@endphp
<div class="card p-5">
    <div class="flex items-center gap-2 text-sm" style="color:var(--muted)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>
        </svg>
        Current plan
    </div>
    <p class="mt-2 text-lg font-semibold">
        {{ ucfirst($subscription->plan) }}
        <span class="badge {{ $active ? 'badge-success' : '' }}">{{ ucfirst($subscription->status) }}</span>
    </p>
    @if ($subscription->renewsAt !== null)
        <p class="mt-1 text-xs" style="color:var(--faint)">Renews {{ $subscription->renewsAt->format('M j, Y') }}</p>
    @endif
    <a href="{{ route('billing.overview') }}" class="mt-3 inline-block text-sm font-medium underline underline-offset-2" style="color:var(--accent)">Manage billing →</a>
</div>
