
pm2 start "php artisan serve --host=0.0.0.0 --port=8012" --name integral-api
pm2 start npm --name "integral" -- run dev
