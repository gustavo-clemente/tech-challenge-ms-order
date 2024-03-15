FROM php:8.1-alpine

EXPOSE 8000

WORKDIR /var/www/html

RUN apk --no-cache add \
    git \
    curl \
    openssl \
    bash \
    unzip \
    zlib-dev \
    sqlite-dev \
    libzip-dev \
    postgresql-dev \
    && docker-php-ext-install zip bcmath sockets 

RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install pdo_sqlite

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; exit(1); } echo PHP_EOL;"

RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN php -r "unlink('composer-setup.php');"

COPY . .

RUN composer install

ENTRYPOINT [ "php", "artisan", "serve"]