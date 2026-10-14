# Deploying the demo

The hosted demo runs on [Render](https://render.com)'s free plan from the repository's
`Dockerfile`, configured by `render.yaml`.

## What the demo does

| Setting | Effect |
|---|---|
| `DEMO_MODE=true` | The AI coach answers with pre-written tips (no AI provider, no cost); weekly plans use the built-in fallback; the login page shows **Try the demo**, which opens a shared, pre-filled account whose password, profile and email can't be changed. Signup stays open. |
| SQLite inside the container | Free with no expiry. The database resets to the seeded demo whenever the container restarts. |
| `TRUSTED_PROXIES=*` | Render terminates HTTPS at its proxy; this makes links `https://` and lets rate limits see real visitor IPs. |
| `MAIL_MAILER=log` | No emails are sent; they appear in the service logs. |

## First deploy

1. Sign in to Render with GitHub and allow access to `Shakeelkhan8/ai-mentor-health`.
2. **New → Blueprint**, pick the repository and the `main` branch. Render reads `render.yaml` and proposes one free web service.
3. **Apply**. The first build takes a few minutes (it builds assets, installs PHP dependencies and the image).
4. When the service is live, open its URL: `/up` should show `{"status":"ok"}`, and `/login` should show **Try the demo**.

Pushes to `main` redeploy automatically.

## Free plan trade-offs

- **Sleeps when idle.** After about 15 minutes without traffic the service stops; the next visit takes roughly a minute to wake it.
- **Resets on wake and on deploy.** With SQLite in the container, every start begins from a fresh demo. Accounts that visitors created are removed too.
- **No persistent uploads.** Profile photos are stored in the container and disappear on restart.

## Keeping data instead

Create a PostgreSQL database (Render, Neon, Supabase…), then set on the service:

```
DB_CONNECTION=pgsql
DATABASE_URL=postgres://…
APP_KEY=base64:…   # generate with: php artisan key:generate --show
```

With a persistent database, set a fixed `APP_KEY` too. Otherwise each restart generates a new key, which logs everyone out. CI already runs the test suite on PostgreSQL.

## Turning the real AI coach on

Set `DEMO_MODE=false` and `RAPIDAPI_KEY`. Free accounts are then limited to `FREE_COACH_MESSAGES_PER_DAY` coach messages (default 10). Every account can still create more accounts, so consider the cost before doing this on a public URL.

## Running the image locally

```bash
docker build -t ai-mentor-health-demo .
docker run --rm -p 8080:8080 -e DEMO_MODE=true -e DB_CONNECTION=sqlite -e DB_DATABASE=/app/database/demo.sqlite -e MAIL_MAILER=log ai-mentor-health-demo
```

Then open http://localhost:8080.
