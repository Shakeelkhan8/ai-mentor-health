# syntax=docker/dockerfile:1

# Production image: FrankenPHP (PHP 8.4) serving Laravel, with Vite assets built in a
# separate stage. Used for the hosted demo (render.yaml); see docs/DEPLOY.md.

# ---- Frontend assets (Vite) ----------------------------------------------------
FROM node:22.22-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json vite.config.js tailwind.config.js postcss.config.js ./
RUN npm ci --no-audit --no-fund
COPY resources ./resources
RUN npm run build

# ---- Application -----------------------------------------------------------------
FROM dunglas/frankenphp:1.12.7-php8.4-bookworm

# The PHP defaults already cover what composer.lock requires. Added: PostgreSQL (so the
# database can be switched from SQLite by env), opcache, and zip for Composer.
RUN install-php-extensions pdo_pgsql opcache zip

# PHP's production settings. Without a php.ini, PHP displays errors and deprecation
# notices in responses (leaking file paths) and in command output.
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && printf 'expose_php = Off\n' > "$PHP_INI_DIR/conf.d/zz-app.ini"

COPY --from=composer:2.10.3 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

WORKDIR /app

# Dependencies first, so code changes don't invalidate this layer.
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --no-scripts --no-autoloader

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --no-dev --optimize \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod +x docker/start.sh

# Run as an unprivileged user. Only the paths the app writes to are owned by it.
RUN useradd --create-home --uid 1000 app \
    && chown -R app:app storage bootstrap/cache database public/assets/users public/assets/doctors /config/caddy /data/caddy
USER app

EXPOSE 8080
CMD ["docker/start.sh"]
