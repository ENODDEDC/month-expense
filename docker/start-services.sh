#!/bin/sh

# Exit immediately if a command exits with a non-zero status.
set -e

# Run database migrations
php artisan migrate --force

# Start supervisord
exec supervisord -c /etc/supervisord.conf