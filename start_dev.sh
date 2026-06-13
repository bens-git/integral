#!/bin/bash

APP_URL=$(grep "^APP_URL=" .env | cut -d= -f2)
APP_URL=${APP_URL:-http://localhost:8010}

pm2 start "php artisan serve --host=0.0.0.0 --port=8010" --name integral-api
pm2 start npm --name "integral" -- run dev

php artisan ziggy:generate --url="${APP_URL}"
