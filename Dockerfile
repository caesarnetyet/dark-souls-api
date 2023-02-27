FROM php:8.1.16-fpm

WORKDIR /app

RUN docker-php-ext-install bcmath

RUN apt-get update && sudo apt-get install git

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN composer install

COPY . .

CMD ["php", "artisan serve"]