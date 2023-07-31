#!/bin/bash

php artisan route:clear
php artisan config:clear
php artisan cache:clear

npm run dev

./vendor/bin/sail up
