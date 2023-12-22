FROM ubuntu:22.04

WORKDIR /app
COPY . /app

ARG DEBIAN_FRONTEND=noninteractive

RUN apt update 
RUN apt install -y nginx curl unzip apt-utils
RUN apt install -y software-properties-common
RUN add-apt-repository -y ppa:ondrej/php
RUN apt install -y php8.1-cli php8.1-fpm php8.1-dom php8.1-xml php8.1-curl

RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php

RUN HASH=`curl -sS https://composer.github.io/installer.sig`
RUN php -r "if (hash_file('SHA384', '/tmp/composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

# RUN apt install -y php8.1-dom php8.1-xml php8.1-curl

RUN rm -rf /etc/nginx/sites-enabled
COPY nginx /etc/nginx

RUN composer install
RUN php artisan key:generate
RUN php artisan storage:link

RUN chown www-data:www-data -R /app

ENV LOG_CHANNEL=stderr

RUN chmod +x start.sh

CMD ./start.sh

EXPOSE 80
