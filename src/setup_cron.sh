#!/bin/bash
# ✅ FILE: src/setup_cron.sh

# Define the full path to PHP and your project directory
PHP_PATH=$(which php)
PROJECT_DIR=$(cd "$(dirname "$0")" && pwd)
CRON_FILE="$PROJECT_DIR/cron.php"

# Define the cron job entry
CRON_JOB="0 9 * * * $PHP_PATH $CRON_FILE > /dev/null 2>&1"

# Check if job already exists to avoid duplicates
(crontab -l 2>/dev/null | grep -v "$CRON_FILE"; echo "$CRON_JOB") | crontab -

echo "✅ CRON job installed to run daily at 9 AM."
echo "Command: $PHP_PATH $CRON_FILE"
