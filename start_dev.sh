
pm2 start "php artisan serve --host=0.0.0.0 --port=8010" --name integral-api
pm2 start npm --name "integral" -- run dev
