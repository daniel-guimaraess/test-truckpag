FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    nano \
    cron \
    bash \
    curl \
    libpng-dev \
    libzip-dev \
    zlib1g-dev \
    unzip \
    procps \
    && docker-php-ext-install gd zip pdo pdo_mysql pcntl

RUN docker-php-ext-configure pcntl --enable-pcntl

RUN ln -snf /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime \
    && echo "America/Sao_Paulo" > /etc/timezone

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

#Copiando projeto
COPY . /var/www/html
COPY .env /var/www/html/.env

#Copiando cron config
COPY cron_backend /etc/cron.d/cron_backend
RUN chmod 0644 /etc/cron.d/cron_backend

#Copiando entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

EXPOSE 9000
