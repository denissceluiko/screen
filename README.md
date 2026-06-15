# Iris Screen

A self-hosted digital signage manager. Create slideshows, assign them to named screens, and point any browser at a screen URL to run a live, auto-updating display.

## How it works

- **Slides** are images uploaded and stored on a dedicated disk.
- **Slideshows** are ordered collections of slides with a configurable switch interval.
- **Screens** have a unique slug and are assigned a slideshow. Each screen polls for changes via Livewire and updates in-browser without a full reload.
- The display URL (`/s/{slug}`) is public and requires no login — intended to run on a TV or kiosk browser.
- Slide files are served through a signed token URL with ETags and `Cache-Control` for efficient browser caching.

## Stack

- PHP 8.5, Laravel 13, Livewire 4
- Filament 5 (multi-tenant admin panel at `/t/{team}`, super-admin at `/admin`)
- MySQL, Redis
- Vite 8

## Local development

Requires [Docker](https://www.docker.com/) and [Laravel Sail](https://laravel.com/docs/sail).

```bash
cp .env.example .env
composer install
vendor/bin/sail up -d
vendor/bin/sail artisan key:generate
vendor/bin/sail artisan migrate
vendor/bin/sail npm install && vendor/bin/sail npm run build
```

Open `http://localhost` and log in at `/admin` to create your first user and team, then manage content at `/t/{team}`.

## Deployment

```bash
composer run deploy-latest
```

This runs: maintenance mode → git pull → `composer install --no-dev` → `npm run build` → migrate → cache → queue restart → back online.
