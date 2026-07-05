# syntax=docker/dockerfile:1

# --- Stage 1: build Vite assets ---
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# --- Stage 2: runtime (shared curated base: gd/imagick/intl/... + opcache on) ---
FROM registry.celuiko.com/celuiko/php:8.5-fpm-nginx

WORKDIR /var/www/html

# Composer deps first so this layer is cached until composer.lock changes.
COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# App code + built assets from the node stage.
COPY --chown=www-data:www-data . .
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build

# Optimized autoloader; post-autoload-dump runs package:discover + filament:upgrade
# (publishes Filament assets into the image). Config/route caching is done at
# runtime in deploy.sh because it depends on .env.
RUN composer dump-autoload --optimize --no-dev
