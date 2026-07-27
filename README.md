# Cbox ID Billing

**`cboxdk/laravel-id-billing`** — the billing console for Cbox ID, as a drop-in
package. Data **and** UI in one place: a **Billing** area (plan, invoices,
usage-vs-limit), a dashboard **current-plan** card, and gates — all plugged into
[`laravel-console-kit`](https://github.com/cboxdk/laravel-console-kit) and gated on the
`billing` feature, which is active only when the [billing client](https://github.com/cboxdk/laravel-billing-client)
is wired.

**Optional by design.** Cbox ID is free to self-host with no limits and no licence
key, and it never depends on this package. Install it only where you actually bill
someone — then Billing lights up and entitlements can be metered
(`CBOX_ID_ENTITLEMENTS=metered`). Leave it out and nothing about the application
changes.

## What it adds

- **Nav** — a `Billing` area with *Overview* and *Invoices*, gated on `billing`.
- **Overview** — current plan + status, usage-vs-limit meters (from `UsageSummary`),
  and a **Manage billing** button that hands off to the billing portal.
- **Invoices** — the org's invoice history.
- **Dashboard card** — the current plan, on the overview page.
- **Route gate** — `console.feature:billing`, so the routes 404 without billing.

All data comes from the billing client's `BillingManagement`; this package renders it
in the host console's shell.

## Install (SaaS)

```bash
composer require cboxdk/laravel-id-billing
```

Requires the billing client configured (so `BillingManagement` is bound) and a host
console that has adopted `laravel-console-kit`.

## License

**MIT.** See [LICENSE](LICENSE).
