#!/bin/bash

REVERB_PORT=$(grep "^REVERB_PORT=" .env | cut -d= -f2)
REVERB_HOST=$(grep "^REVERB_HOST=" .env | cut -d= -f2)
REVERB_APP_KEY=$(grep "^REVERB_APP_KEY=" .env | cut -d= -f2)
REVERB_APP_ID=$(grep "^REVERB_APP_ID=" .env | cut -d= -f2)
APP_URL=$(grep "^APP_URL=" .env | cut -d= -f2)
APP_URL=${APP_URL:-http://localhost:8010}

pm2 start "php artisan serve --host=0.0.0.0 --port=8010" --name integral-api
pm2 start npm --name "integral" -- run dev
pm2 start "php artisan reverb:start --host=${REVERB_HOST} --port=${REVERB_PORT}" --name "integral-reverb"

php artisan ziggy:generate --url="${APP_URL}"
