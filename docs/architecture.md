# AI Mentor Health — Architecture

## Product
Personal AI wellness coach: habits, fitness, nutrition, sleep, stress.
Not a doctor marketplace. Not a diagnostic medical device.

## Layering

```text
HTTP (Controllers, FormRequests, Middleware)
        ↓
Application (Actions, Domain Services)
        ↓
Domain (Enums, Contracts, Models, Policies)
        ↓
Infrastructure (AI clients, Stripe, mail, storage)
```

### Rules
1. Controllers orchestrate HTTP only — no business rules.
2. Actions/Services own use-cases (onboarding, check-in, coach reply, billing webhook).
3. Domain never imports Infrastructure HTTP clients directly — use contracts.
4. Feature flags in `config/mentor.php` gate deferred modules (`care_marketplace`).

## Domains

| Domain | Responsibility |
|--------|----------------|
| `Identity` | User profile, onboarding goals, focus areas |
| `Coaching` | Conversations, AI coach replies, safety framing |
| `Habits` | Habit definitions, daily check-ins, streaks |
| `Plans` | Weekly personalized plans |
| `Billing` | Subscriptions, Stripe webhooks, entitlements |
| `Shared` | Actions base, domain exceptions, shared contracts |
| `Legacy Care` | Doctors/appointments/hospitals — feature-flagged off |

## Key paths

```text
app/Domains/{Identity,Coaching,Habits,Plans,Billing,Shared}/
app/Infrastructure/Ai/
app/Http/Controllers/Web/{Onboarding,Coaching,Habits,Plans,Billing}/
app/Http/Requests/{...}/
config/mentor.php
docs/
```

## Dependency example
`CoachingService` → `AiCoachClient` (contract) → `RapidApiCoachClient` (infra)

Swap AI providers by rebinding in `DomainServiceProvider` — domain code stays unchanged.

`StartSubscriptionCheckout` → `BillingGateway` → `StripeBillingGateway`

## Safety
Product copy and coach/plan prompts frame AI Mentor Health as **general wellness guidance only** — not diagnosis or medical care.

## Docs
- [DEMO.md](DEMO.md) — recruiter/demo walkthrough  
- [domains.md](domains.md) — conventions + route map  

