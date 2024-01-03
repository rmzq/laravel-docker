#!/bin/bash

git pull &&\

# Install/update composer dependecies
/bin/php8.1 /usr/local/bin/composer install &&\

/bin/php8.1 /usr/local/bin/composer dumpautoload &&\

/bin/php8.1 artisan optimize &&\


chown wwww-data:www-data -R .