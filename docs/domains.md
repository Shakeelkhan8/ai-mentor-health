# Domain conventions

## Adding a new use-case
1. Put business logic in `app/Domains/{Domain}/Actions` or `Services`.
2. Add FormRequest under `app/Http/Requests/{Domain}`.
3. Keep controller thin: authorize → validate → action → response.
4. Add a Feature test under `tests/Feature/{Domain}`.

## Naming
- Actions: `CreateHabitCheckIn`, `GenerateWeeklyPlan`, `StartCheckoutSession`
- Services: longer-lived orchestration (`CoachingService`, `SubscriptionService`)
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
