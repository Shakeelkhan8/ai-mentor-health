# AI Mentor Health — Demo walkthrough (5–10 minutes)

Goal: show the **aha moment** — the product remembers you, coaches you, tracks habits, and builds a personalized weekly plan.

> Safety (keep visible, not overwhelming):  
> *AI Mentor Health provides general wellness guidance and does not diagnose conditions or replace medical professionals.*

## Prerequisites

- App running (`php artisan serve`)
- Migrated database
- Optional but better for full demo:
  - `RAPIDAPI_KEY` set (otherwise coach/plan fall back or show config errors with plan fallback)
  - `STRIPE_SECRET`, `STRIPE_KEY`, `STRIPE_PRICE_ID` set for Checkout (test mode)

## Script

### 1. Create account (~1 min)

1. Open `/register`
2. Create a user and verify email if your local setup requires it
3. Sign in

### 2. Complete health onboarding (~1–2 min)

1. You should land on `/onboarding` until complete
2. Choose focus areas (e.g. Sleep + Nutrition)
3. Set a primary goal (e.g. “Sleep consistently and drink more water”)
4. Confirm timezone
5. Submit — starter habits are created from your focus areas

**Aha cue:** the product now has a profile it can personalize against.

### 3. Chat with AI coach (~2 min)

1. Sidebar → **AI Coach** (`/ai/coach`)
2. Ask something goal-aligned, e.g.  
   “I keep waking up tired. What’s a realistic wind-down routine?”
3. Confirm the reply is wellness-oriented and the disclaimer is visible
4. Send a follow-up so history persists in the thread

**Aha cue:** coaching feels personal, not a generic FAQ bot.

### 4. Complete a habit check-in (~1 min)

1. Open **Dashboard** (`/dashboard`)
2. Find today’s starter habits
3. Mark one **Done** (and optionally another **Skip**)
4. Refresh — status badges update for today

**Aha cue:** behavior is tracked, not just chatted about.

### 5. Generate a personalized weekly plan (~2 min)

1. Sidebar → **Weekly Plan** (`/plans/weekly`)
2. Click **Generate plan**
3. Review title, summary, and action items (`action` / `category` / `target`)
4. Optionally **Regenerate** — same week updates in place (no duplicate week rows)

**Aha cue:** plan uses your goal, focus areas, habits, and recent check-ins (AI JSON with safe fallback).

### 6. Show subscription / billing (test mode) (~1–2 min)

1. Sidebar → **Billing** (`/billing`)
2. Explain **Free vs Pro** (soft CTA — features are not locked)
3. Click **Subscribe with Stripe** (requires Stripe test config)
4. Complete Checkout with a [Stripe test card](https://stripe.com/docs/testing) (`4242…`)
5. Land on success; with webhooks configured, subscription status becomes `active`

If Stripe isn’t configured, show the Billing page warning and explain Pro is demonstrated conceptually.

## Talking points for reviewers

- Domain layering: HTTP → Actions/Services → Contracts → Infrastructure  
- AI provider is swappable (`AiCoachClient`)  
- Weekly plans validate a strict JSON schema before save  
- Stripe Checkout + signed webhooks sync `subscriptions`  
- Care marketplace (doctors/hospitals) is explicitly deferred behind a feature flag  

## Deferred (say this out loud)

Not part of the MVP demo:

- Doctor marketplace  
- Hospital features  
- Clinical diagnosis  
- Medical decision support  
