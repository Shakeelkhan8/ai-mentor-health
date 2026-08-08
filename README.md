# AI Mentor Health

**AI Mentor Health is an AI-powered wellness companion that helps users build healthier habits through personalized coaching, progress tracking, and weekly wellness plans.**

It is **not** a generic chatbot. It remembers user goals and focus areas, tracks daily habit check-ins, generates personalized weekly plans, and supports a Pro subscription via Stripe (test mode).

> AI Mentor Health provides general wellness guidance and does not diagnose conditions or replace medical professionals.

## Why this project

Portfolio + MVP foundation for a B2C wellness product:

- Domain-oriented Laravel architecture (Identity, Coaching, Habits, Plans, Billing)
- Swappable AI provider via `AiCoachClient`
- Stripe Checkout + webhooks for subscriptions
- Feature flags for deferred care-marketplace modules

## Core product loop

1. **Onboard** — goals, focus areas, timezone  
2. **Coach** — personalized AI wellness chat with safety framing  
3. **Check in** — daily habit progress  
4. **Plan** — AI weekly plan (strict JSON + safe fallback)  
5. **Subscribe** — optional Pro checkout (Stripe test mode; features not gated yet)

## Architecture

```text
HTTP (Controllers, FormRequests, Middleware)
        ↓
Application (Actions, Domain Services)
        ↓
Domain (Enums, Contracts, Models)
        ↓
Infrastructure (RapidAPI coach, Stripe billing)
```

See [docs/architecture.md](docs/architecture.md) and [docs/domains.md](docs/domains.md).

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
# configure DB (MySQL recommended locally)
php artisan migrate
npm install && npm run build
php artisan serve
```

PHP **8.3** recommended for this lockfile.

### Important env vars

| Variable | Purpose |
|----------|---------|
| `MENTOR_PRODUCT_NAME` | Product display name |
| `FEATURE_AI_COACH` | AI coach routes |
| `FEATURE_HABIT_TRACKING` | Habit check-ins |
| `FEATURE_WEEKLY_PLANS` | Weekly plans |
| `FEATURE_SUBSCRIPTIONS` | Billing UI |
| `FEATURE_CARE_MARKETPLACE` | Doctors/hospitals (**keep `false`**) |
| `RAPIDAPI_KEY` | AI coach provider |
| `STRIPE_KEY` / `STRIPE_SECRET` | Stripe test keys |
| `STRIPE_WEBHOOK_SECRET` | Webhook verification (required outside local/testing) |
| `STRIPE_PRICE_ID` | Recurring Price id for Pro |

## Demo

Follow the 5–10 minute walkthrough: **[docs/DEMO.md](docs/DEMO.md)**

## Ops

Deploy checklist, secrets, and observability notes: **[docs/ops.md](docs/ops.md)**

## Tests

```bash
php artisan test
```

CI runs the full suite on push/PR via GitHub Actions (`.github/workflows/ci.yml`).

## Pricing model (MVP)

| Tier | What you get |
|------|----------------|
| **Free** | Onboarding, AI coach, habit check-ins, weekly plans |
| **Pro** | Same experience today + Stripe subscription concept for future entitlements |

Features are **not** blocked behind Pro yet (soft CTA only).

## Explicitly deferred / out of scope

- Doctor marketplace  
- Hospital finder / appointments  
- Clinical diagnosis  
- Medical decision support  

Legacy marketplace code remains feature-flagged off (`FEATURE_CARE_MARKETPLACE=false`).

## License

Application code in this repository follows the project’s existing license terms. Laravel framework components remain under the MIT license.
