<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// The host's auth guard, plus console-kit's feature guard: these routes 404 unless
// the `billing` feature is active (i.e. the billing client is wired — the SaaS).
Route::middleware(['web', 'platform.auth', 'console.feature:billing'])->group(function (): void {
    Volt::route('/billing', 'billing.overview')->name('billing.overview');
    Volt::route('/billing/invoices', 'billing.invoices')->name('billing.invoices');
});
