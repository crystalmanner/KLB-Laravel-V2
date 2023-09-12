#!/usr/bin/env bash
# https://github.com/olivergondza/bash-strict-mode
set -euo pipefail
trap 's=$?; echo >&2 "$0: Error on line "$LINENO": $BASH_COMMAND"; exit $s' ERR
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null && pwd )"
CWD="$(pwd)"

# Must have git
if ! command -V "git" &> /dev/null; then
    echo "git is required to run this script."
    exit 1;
fi

# Must have docker
if ! command -V "docker" &> /dev/null; then
    echo "docker is required to run this script."
    exit 1;
fi

# If the .env does not exist, get it from KLB-Laravel-V2
if [ ! -f "${CWD}/.env" ]; then
    cp -v "${CWD}/.env.example" "${CWD}/.env"
fi

# Confirm that required environment variables are set, and if not, stop here.
# https://unix.stackexchange.com/a/294400/260936
# https://codewithhugo.com/just-enough-bash-to-be-dangerous/#inject-env-into-your-bash-sessionenvironment
# Bagisto required .env variables:
# https://webkul.com/blog/laravel-ecommerce-website/
envs=(
    'APP_URL'
    'DB_CONNECTION'
    'DB_DATABASE'
    'DB_HOST'
    'DB_PASSWORD'
    'DB_USERNAME'
)

array_length=${#envs[@]}
for (( key=0; key<array_length; key++ ));
do
    # Get the value from the .env
    # https://en.wikipedia.org/wiki/Cat_%28Unix%29#Useless_use_of_cat
    line=$(grep "${envs[$key]}" < "${CWD}/.env" | xargs)
    envValue="${line#*${envs[$key]}=}"

    if [ -z "${envValue}" ]; then
        echo "Please specify a value for ${envs[$key]} in ${CWD}/.env."
        exit 1;
    fi
done

git fetch --all

# Actually build all of the necessary containers and link them together
docker-compose --env-file "${CWD}"/.env up -d --build

echo "CWD: ${CWD}"

# Installs Composer packages
docker exec kalistabeauty composer install --no-interaction --prefer-dist --optimize-autoloader
# Generate an APP_KEY for Laravel/Bagisto
docker exec kalistabeauty php artisan key:generate
# Publishes all package files
docker exec kalistabeauty php artisan vendor:publish --force --all

# Install Bagisto
# This should only be run one time
# docker exec kalistabeauty php artisan bagisto:install
docker exec kalistabeauty php artisan migrate:fresh
docker exec kalistabeauty php artisan db:seed
docker exec kalistabeauty php artisan storage:link
docker exec kalistabeauty composer dumpautoload
# Install all NPM packages from the root folder
docker exec kalistabeauty npm install
docker exec kalistabeauty npm run prod
# Installs all NPM packages from packages/KLB/Themes
docker exec kalistabeauty bash -c "cd packages/KLB/Themes; npm install; npm run prod"
docker exec kalistabeauty php artisan db:seed --class='KLB\Themes\Database\Seeders\KLBDatabaseSeeder'

# After this, you'll need to:
# Set the theme for the default channel to KLB Theme
# Add a new Attribute family with the code "shopify" and the name "Shopify"
# docker exec kalistabeauty php artisan db:seed --class='KLB\Themes\Database\Seeders\KLBDatabaseSeeder'

# Install Bagisto
# Delete the storage symlink if it exists already
# The [/var/www/html/public/storage] link has been connected to [/var/www/html/storage/app/public].
# docker exec kalistabeauty unlink public/storage || true
# docker exec kalistabeauty unlink storage/app/public || true
# Drops and recreates all tables
# docker exec kalistabeauty php artisan migrate:fresh
# docker exec kalistabeauty php artisan db:seed
# docker exec kalistabeauty php artisan vendor:publish --force --all
# docker exec kalistabeauty php artisan storage:link
