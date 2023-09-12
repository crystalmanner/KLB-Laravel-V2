#!/usr/bin/env bash
# https://github.com/olivergondza/bash-strict-mode
set -euo pipefail
trap 's=$?; echo >&2 "$0: Error on line "$LINENO": $BASH_COMMAND"; exit $s' ERR
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null && pwd )"
CWD="$(pwd)"

# Must have git
if ! command -V "php" &> /dev/null; then
    echo "php is required to run this script."
    exit 1;
fi

# Must have npm
if ! command -V "npm" &> /dev/null; then
    echo "npm is required to run this script."
    exit 1;
fi

cd ~/packages/KLB/Themes/
# Combines all JS and CSS the same way that is done on kalistabeauty.co
# npm run prod
npm run dev
cd ~
php artisan vendor:publish --force --all
# This clears and caches the routes
php artisan route:cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
composer dump-autoload
