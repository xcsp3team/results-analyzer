cd /home/gibe3814/repositories/location/
/usr/local/bin/php artisan  queue:work --stop-when-empty --max-time=50 >> storage/logs/queue.log  2>&1