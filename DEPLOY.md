Recommended free hosting for long-term use

Recommendation (best free-ish for Laravel): Fly.io
- Persistent runtime (no forced sleep) on free tier for small apps
- Easy custom domain/TLS later
- Can add managed Postgres via Fly Postgres

Alternative: Render
- Simple GitHub auto-deploys; free web services sleep when idle (still works for light usage)

Quick deploy steps (Fly.io)

1. Sign up at https://fly.io and install `flyctl`.

2. Build assets and commit (recommended):

```bash
# build frontend locally
npm install
npm run build

# install php deps and commit vendor if you prefer building in image
composer install --no-dev --optimize-autoloader
git add .
git commit -m "Prepare for deploy"
```

3. Create app and deploy:

```bash
flyctl auth login
flyctl launch --name client-portal --no-deploy
# edit fly.toml if needed, then
flyctl deploy
```

4. Add Postgres (managed) on Fly if you need DB:

```bash
flyctl postgres create --name client-portal-db
# follow instructions to attach the DB and set FLY_POSTGRES_URL or DATABASE_URL
```

5. Set environment variables on Fly (APP_KEY, DB credentials, MAIL settings):

```bash
flyctl secrets set APP_KEY="$(php artisan key:generate --show)" \
DATABASE_URL="your_database_url_here"
```

6. Run migrations:

```bash
flyctl ssh console -C "cd /app && php artisan migrate --force"
```

Notes and next steps
- For production-grade setup, replace the built-in PHP server with `nginx` + `php-fpm` in the Dockerfile and run queue workers.
- If you want, I can: create a small `fly.toml`, convert the Dockerfile to use `nginx`, or prepare a Render deployment guide instead.
