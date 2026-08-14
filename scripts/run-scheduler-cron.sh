#!/bin/sh

cd /var/www/html || exit 1

php artisan schedule:run >> /var/log/cron.log 2>&1
