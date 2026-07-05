#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")"

COMPOSE="docker compose -f compose.prod.yml"

# Refresh build context to latest master. Ignored files (.env, storage/) are NOT
# touched by `git clean -fd` — it only removes untracked, not ignored, files.
git fetch origin
git reset --hard origin/master
git clean -fd

# Build the new image while the old container keeps serving (atomic-ish deploy).
$COMPOSE build

# Swap to the new image.
$COMPOSE up -d

# Runtime steps — need .env, so they run inside the new container.
$COMPOSE exec -T app php artisan migrate --force
$COMPOSE exec -T app php artisan storage:link --relative
$COMPOSE exec -T app php artisan icons:cache
$COMPOSE exec -T app php artisan optimize
$COMPOSE exec -T app php artisan filament:optimize
