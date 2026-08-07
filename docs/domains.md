# Domain conventions

## Adding a new use-case
1. Put business logic in `app/Domains/{Domain}/Actions` or `Services`.
2. Add FormRequest under `app/Http/Requests/{Domain}`.
3. Keep controller thin: authorize → validate → action → response.
4. Add a Feature test under `tests/Feature/{Domain}`.

## Naming
- Actions: `RecordHabitCheckIn`, `GenerateWeeklyPlan`, `StartSubscriptionCheckout`, `HandleStripeWebhook`
- Services: longer-lived orchestration (`CoachingService`, `WeeklyPlanGenerator`, `SubscriptionSyncService`)
- Enums: backed string enums for DB/API stability

## Models
New Eloquent models live under `app/Domains/{Domain}/Models`.
Legacy marketplace models remain in `app/Models` until migrated or removed.

### Current domain tables
| Table | Domain | Notes |
|-------|--------|--------|
| `user_profiles` | Identity | 1:1 with users, JSON focus areas |
| `habits` | Habits | per-user habits + focus/frequency enums |
| `habit_check_ins` | Habits | unique per habit/day |
| `coach_conversations` | Coaching | chat threads |
| `coach_messages` | Coaching | role + content |
| `weekly_plans` | Plans | unique per user/week_start |
| `subscriptions` | Billing | Stripe ids + status |

## HTTP surface (MVP)

| Area | Routes | Notes |
|------|--------|--------|
| Onboarding | `GET/POST /onboarding` | Required before app |
| Dashboard | `GET /dashboard` | Habits check-in + soft Pro CTA |
| Coach | `GET/POST /ai/coach*` | `feature:ai_coach` |
| Weekly plans | `GET /plans/weekly`, `POST /plans/weekly/generate` | `feature:weekly_plans` |
| Billing | `GET /billing`, `POST /billing/checkout`, success/cancel | `feature:subscriptions` |
| Stripe webhook | `POST /stripe/webhook` | CSRF excluded; signature verified when secret set |

## Deferred (not MVP)
- Doctor marketplace  
- Hospital finder / appointments  
- Clinical diagnosis / medical decision support  

Gate: `FEATURE_CARE_MARKETPLACE=false`.
