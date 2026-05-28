web: node /assets/scripts/prestart.mjs /assets/nginx.template.conf /nginx.conf && (php-fpm -y /assets/php-fpm.conf & nginx -c /nginx.conf)
worker: php artisan queue:work --sleep=3 --tries=3 --max-time=3600
