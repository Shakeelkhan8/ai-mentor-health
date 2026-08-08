# Operations runbook — AI Mentor Health

## Environments

| Env | Purpose |
|-----|---------|
| `local` | Developer machines (WAMP/PHP 8.3 recommended) |
| `testing` | PHPUnit (`phpunit.xml`, sqlite `:memory:`) |
| `production` | Real users — `APP_DEBUG=false`, secrets only via env |

## Required secrets / config

| Variable | Required for |
|----------|----------------|
| `APP_KEY` | App boot |
| `DB_*` | MySQL in non-test envs |
| `RAPIDAPI_KEY` | Live AI coach |
| `STRIPE_SECRET` / `STRIPE_KEY` / `STRIPE_PRICE_ID` | Checkout |
| `STRIPE_WEBHOOK_SECRET` | **Required** outside `local`/`testing` |
| Feature flags | See `.env.example` — keep `FEATURE_CARE_MARKETPLACE=false` |

## Deploy checklist

1. `composer install --no-dev --optimize-autoloader`
2. `php artisan migrate --force`
3. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
4. Confirm `APP_DEBUG=false`
5. Point Stripe webhook to `POST /stripe/webhook`
6. Smoke: register → verify email → onboard → coach → check-in → weekly plan → billing

## Observability

- Laravel logs: `storage/logs/laravel.log`
- Domain errors use `DomainException` + `error_code` (JSON API mapping in `App\Exceptions\Handler`)
- Recommended next: install Sentry (`sentry/sentry-laravel`) and set `SENTRY_LARAVEL_DSN`

## Feature flags

| Flag | Default intent |
|------|----------------|
| `FEATURE_AI_COACH` | Core |
| `FEATURE_HABIT_TRACKING` | Core |
| `FEATURE_WEEKLY_PLANS` | Core |
| `FEATURE_SUBSCRIPTIONS` | Soft CTA / Stripe test |
| `FEATURE_CARE_MARKETPLACE` | **Off** — deferred doctors/hospitals |

Unknown feature middleware names return 404.

## Demo

See [DEMO.md](DEMO.md) for the 5–10 minute recruiter walkthrough.
