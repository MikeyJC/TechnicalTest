FROM php:8.1-fpm-bullseye

WORKDIR /var/www/html

RUN echo 'Acquire::AllowInsecureRepositories "true";' > /etc/apt/apt.conf.d/99insecure && \
    rm -rf /var/lib/apt/lists/* /var/cache/apt/archives/* && \
    apt-get update --allow-insecure-repositories && \
    apt-get install -y --allow-unauthenticated \
        unzip zip curl git libpng-dev libonig-dev libxml2-dev libzip-dev apt-utils && \
    docker-php-ext-install pdo pdo_mysql mbstring zip && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /var/cache/apt/archives/*

# INSTALL LARAVEL (this is key!)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer create-project laravel/laravel .
