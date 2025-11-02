#!/bin/bash
set -euo pipefail

cd /var/www/html

# Validate expected environment variables
echo "AdamRMS - Checking for Environment Variables"
: "${DB_HOSTNAME?AdamRMS - DB_HOSTNAME is not set}"
: "${DB_DATABASE?AdamRMS - DB_DATABASE is not set}"
: "${DB_USERNAME?AdamRMS - DB_USERNAME is not set}"
: "${DB_PASSWORD?AdamRMS - DB_PASSWORD is not set}"

# Database migration & seed
echo "AdamRMS - Starting Migration Script"
php vendor/bin/phinx migrate -e production

if [[ "${SEED_ON_START:-true}" == 'true' ]]; then
    echo "AdamRMS - Running seeds"
    php vendor/bin/phinx seed:run -e production
else
    echo "AdamRMS - Skipping seeds (SEED_ON_START=${SEED_ON_START:-false})"
fi

if [[ -v DEV_MODE ]] && [[ "${DEV_MODE}" == 'true' ]]; then
    echo "AdamRMS - Running in DEV MODE"
fi

# Start Server
echo "AdamRMS - Starting Apache2"
apache2-foreground
