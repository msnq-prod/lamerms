#!/bin/bash
set -euo pipefail

cd /var/www/html

# Validate expected environment variables
echo "AdamRMS - Checking for Environment Variables"
required_vars=(DB_HOSTNAME DB_DATABASE DB_USERNAME DB_PASSWORD CONFIG_ROOTURL CONFIG_AUTH_JWTKey)
for var in "${required_vars[@]}"; do
    if [[ -z "${!var:-}" ]]; then
        echo "AdamRMS - Missing required environment variable: ${var}" >&2
        exit 1
    fi
done

# Lightweight validation for critical values
if [[ ${#CONFIG_AUTH_JWTKey} -ne 64 ]]; then
    echo "AdamRMS - CONFIG_AUTH_JWTKey must be exactly 64 characters" >&2
    exit 1
fi

if [[ "${CONFIG_ROOTURL}" == */ ]]; then
    echo "AdamRMS - CONFIG_ROOTURL must not end with a trailing slash" >&2
    exit 1
fi

# Database migration & seed
echo "AdamRMS - Starting Migration Script"
# Only the trimmed migration set in db/migrations is executed by default.
# Legacy migrations are archived under legacy/db/migrations for reference.
php vendor/bin/phinx migrate -e production

if [[ "${SEED_ON_START:-true}" == 'true' ]]; then
    echo "AdamRMS - Running MVP seeders"
    php vendor/bin/phinx seed:run -e production \
        -s PositionsSeeder \
        -s DefaultUserSeeder \
        -s ManufacturersSeeder \
        -s AssetCategorySeeder \
        -s MaintenanceJobsStatusesSeeder
else
    echo "AdamRMS - Skipping seeds (SEED_ON_START=${SEED_ON_START:-false})"
fi

if [[ -v DEV_MODE ]] && [[ "${DEV_MODE}" == 'true' ]]; then
    echo "AdamRMS - Running in DEV MODE"
fi

# Start Server
echo "AdamRMS - Starting Apache2"
apache2-foreground
