#!/bin/sh
set -m # Enable job control

# Start php-fpm in the background
php-fpm &

# Start crond
crond -f -l 8

# Bring php-fpm back to the foreground to keep the container running
fg %1
