FROM php:8.1-apache-bullseye

WORKDIR /var/www/html

RUN echo 'Acquire::AllowInsecureRepositories "true";' > /etc/apt/apt.conf.d/99insecure && \
    apt-get update --allow-insecure-repositories && \
    apt-get install -y --allow-unauthenticated \
        libicu-dev \
        unzip zip curl git libpng-dev libonig-dev libxml2-dev libzip-dev apt-utils && \
    docker-php-ext-install intl pdo pdo_mysql mysqli mbstring zip && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /var/cache/apt/archives/*


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
