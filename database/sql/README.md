# Raw SQL fallbacks

If `php artisan migrate --force` cannot run on the cPanel host (e.g. SSH/CLI
disabled, PHP binary not on `$PATH`, or `migrate` errors out), paste the
matching `.sql` file from this folder into **phpMyAdmin → SQL** on the same
database the live app is using.

Each file is **idempotent** — safe to run again if you're not sure whether
it was already applied. They only target the rows / structures they describe.

## Run order

These mirror the Laravel migrations under `database/migrations/`. The
`.cpanel.yml` deploy hook tries to apply them automatically via
`php artisan migrate`. The files here are only needed as a fallback.

| File                                       | What it does                                                                 |
| ------------------------------------------ | ---------------------------------------------------------------------------- |
| `2026_06_04_null_next_billing.sql`         | Nulls `next_billing_date` on rows that never had an ARB recurring billing.   |

## After running by hand

If you applied a `.sql` file manually, tell Laravel it's done so future
`migrate` runs don't try to re-apply it:

```sql
INSERT INTO migrations (migration, batch)
VALUES ('2026_06_04_000001_null_next_billing_for_one_time_plans', 1);
```

(Pick a `batch` number higher than anything already in the `migrations`
table.)
